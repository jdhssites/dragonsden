import { NextResponse } from "next/server"
import { seedDatabase } from "@/lib/seed"
import { isAdmin } from "@/lib/auth"

export async function GET(request: Request) {
  // Check for authorization - only allow admins or specific API key
  const isUserAdmin = await isAdmin()
  const url = new URL(request.url)
  const apiKey = url.searchParams.get("key")
  const isAuthorized = isUserAdmin || apiKey === process.env.SEED_API_KEY

  if (!isAuthorized) {
    return NextResponse.json({ success: false, message: "Unauthorized" }, { status: 401 })
  }

  try {
    await seedDatabase()
    return NextResponse.json({ success: true, message: "Database seeded successfully" })
  } catch (error) {
    console.error("Error seeding database:", error)
    return NextResponse.json({ success: false, message: "Error seeding database" }, { status: 500 })
  }
}

