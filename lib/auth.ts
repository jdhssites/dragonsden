"use server"

import { cookies } from "next/headers"
import { redirect } from "next/navigation"
import { revalidatePath } from "next/cache"
import { hash, compare } from "bcrypt"
import prisma from "./db"

export async function registerUser(formData: FormData) {
  const name = formData.get("name") as string
  const email = formData.get("email") as string
  const password = formData.get("password") as string

  if (!name || !email || !password) {
    return { success: false, message: "All fields are required" }
  }

  try {
    // Check if user already exists
    const existingUser = await prisma.user.findUnique({
      where: { email },
    })

    if (existingUser) {
      return { success: false, message: "User with this email already exists" }
    }

    // Hash the password
    const hashedPassword = await hash(password, 10)

    // Create new user
    const newUser = await prisma.user.create({
      data: {
        name,
        email,
        password: hashedPassword,
        isAdmin: false, // Regular users are not admins
        avatar: "/placeholder.svg?height=100&width=100",
        theme: "system",
        emailNotifications: true,
        darkMode: false,
        language: "en",
      },
    })

    // Set session cookie
    cookies().set("session", newUser.id, {
      httpOnly: true,
      secure: process.env.NODE_ENV === "production",
      maxAge: 60 * 60 * 24 * 7, // 1 week
      path: "/",
    })

    return {
      success: true,
      message: "Registration successful",
      user: {
        id: newUser.id,
        name: newUser.name,
        email: newUser.email,
        isAdmin: newUser.isAdmin,
        avatar: newUser.avatar,
        theme: newUser.theme,
      },
    }
  } catch (error) {
    console.error("Registration error:", error)
    return { success: false, message: "An error occurred during registration" }
  }
}

export async function loginUser(formData: FormData) {
  const email = formData.get("email") as string
  const password = formData.get("password") as string

  if (!email || !password) {
    return { success: false, message: "Email and password are required" }
  }

  try {
    // Find user
    const user = await prisma.user.findUnique({
      where: { email },
    })

    if (!user) {
      return { success: false, message: "Invalid email or password" }
    }

    // Check password
    const passwordMatch = await compare(password, user.password)
    if (!passwordMatch) {
      return { success: false, message: "Invalid email or password" }
    }

    // Set session cookie
    cookies().set("session", user.id, {
      httpOnly: true,
      secure: process.env.NODE_ENV === "production",
      maxAge: 60 * 60 * 24 * 7, // 1 week
      path: "/",
    })

    return {
      success: true,
      message: "Login successful",
      user: {
        id: user.id,
        name: user.name,
        email: user.email,
        isAdmin: user.isAdmin,
        avatar: user.avatar,
        theme: user.theme,
      },
    }
  } catch (error) {
    console.error("Login error:", error)
    return { success: false, message: "An error occurred during login" }
  }
}

export async function logoutUser() {
  cookies().delete("session")
  redirect("/")
}

export async function getCurrentUser() {
  const sessionId = cookies().get("session")?.value

  if (!sessionId) {
    return null
  }

  try {
    const user = await prisma.user.findUnique({
      where: { id: sessionId },
    })

    if (!user) {
      return null
    }

    return {
      id: user.id,
      name: user.name,
      email: user.email,
      isAdmin: user.isAdmin,
      avatar: user.avatar,
      bio: user.bio,
      theme: user.theme,
      preferences: {
        emailNotifications: user.emailNotifications,
        darkMode: user.darkMode,
        language: user.language,
      },
    }
  } catch (error) {
    console.error("Get current user error:", error)
    return null
  }
}

export async function isAdmin() {
  const user = await getCurrentUser()
  return user?.isAdmin || false
}

export async function resetPassword(email: string) {
  // In a real app, you would send a password reset email
  // For this demo, we'll just return a success message
  return { success: true, message: "If an account with that email exists, we've sent a password reset link" }
}

export async function updateUserProfile(formData: FormData) {
  const user = await getCurrentUser()

  if (!user) {
    return { success: false, message: "You must be logged in to update your profile" }
  }

  const name = formData.get("name") as string
  const email = formData.get("email") as string
  const avatar = formData.get("avatar") as string
  const bio = formData.get("bio") as string
  const theme = formData.get("theme") as string
  const emailNotifications = formData.get("emailNotifications") === "on"
  const darkMode = formData.get("darkMode") === "on"
  const language = formData.get("language") as string

  if (!name || !email) {
    return { success: false, message: "Name and email are required" }
  }

  try {
    // Check if email is already taken by another user
    const existingUser = await prisma.user.findUnique({
      where: { email },
    })

    if (existingUser && existingUser.id !== user.id) {
      return { success: false, message: "Email is already in use by another account" }
    }

    // Update user
    await prisma.user.update({
      where: { id: user.id },
      data: {
        name,
        email,
        avatar: avatar || user.avatar,
        bio,
        theme,
        emailNotifications,
        darkMode,
        language,
      },
    })

    revalidatePath("/profile")
    return { success: true, message: "Profile updated successfully" }
  } catch (error) {
    console.error("Update profile error:", error)
    return { success: false, message: "Failed to update profile" }
  }
}

export async function changePassword(formData: FormData) {
  const user = await getCurrentUser()

  if (!user) {
    return { success: false, message: "You must be logged in to change your password" }
  }

  const currentPassword = formData.get("currentPassword") as string
  const newPassword = formData.get("newPassword") as string
  const confirmPassword = formData.get("confirmPassword") as string

  if (!currentPassword || !newPassword || !confirmPassword) {
    return { success: false, message: "All fields are required" }
  }

  try {
    // Get user with password
    const dbUser = await prisma.user.findUnique({
      where: { id: user.id },
    })

    if (!dbUser) {
      return { success: false, message: "User not found" }
    }

    // Check current password
    const passwordMatch = await compare(currentPassword, dbUser.password)
    if (!passwordMatch) {
      return { success: false, message: "Current password is incorrect" }
    }

    if (newPassword !== confirmPassword) {
      return { success: false, message: "New passwords do not match" }
    }

    // Hash new password
    const hashedPassword = await hash(newPassword, 10)

    // Update password
    await prisma.user.update({
      where: { id: user.id },
      data: { password: hashedPassword },
    })

    return { success: true, message: "Password changed successfully" }
  } catch (error) {
    console.error("Change password error:", error)
    return { success: false, message: "Failed to change password" }
  }
}

export async function seedAdminUser() {
  try {
    const existingAdmin = await prisma.user.findFirst({
      where: { isAdmin: true },
    })

    if (!existingAdmin) {
      const hashedPassword = await hash("admin123", 10)

      await prisma.user.create({
        data: {
          name: "Admin",
          email: "admin@example.com",
          password: hashedPassword,
          isAdmin: true,
          avatar: "/placeholder.svg?height=100&width=100",
          bio: "Site administrator",
          theme: "system",
          emailNotifications: true,
          darkMode: false,
          language: "en",
        },
      })

      console.log("Admin user created successfully")
    }
  } catch (error) {
    console.error("Error seeding admin user:", error)
  }
}

export type User = {
  id: string
  name: string
  email: string
  isAdmin?: boolean
  avatar?: string | null
  bio?: string | null
  theme?: string | null
  preferences?: {
    emailNotifications?: boolean | null
    darkMode?: boolean | null
    language?: string | null
  }
}

