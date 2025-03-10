"use server"

import { revalidatePath } from "next/cache"
import { isAdmin, getCurrentUser } from "@/lib/auth"
import prisma from "@/lib/db"
import { slugify } from "@/lib/utils"

export async function getArticles() {
  try {
    const articles = await prisma.article.findMany({
      include: {
        author: {
          select: {
            name: true,
            avatar: true,
          },
        },
      },
      orderBy: {
        date: "desc",
      },
    })

    return articles.map((article) => ({
      id: article.id,
      title: article.title,
      slug: article.slug,
      excerpt: article.excerpt,
      content: article.content.split("\n\n"),
      image: article.image || "/placeholder.svg?height=600&width=800",
      date: article.date.toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
      }),
      readTime: article.readTime,
      category: article.category,
      author: {
        name: article.author.name,
        avatar: article.author.avatar || "/placeholder.svg?height=100&width=100",
      },
    }))
  } catch (error) {
    console.error("Error fetching articles:", error)
    return []
  }
}

export async function getArticleById(id: string) {
  try {
    const article = await prisma.article.findUnique({
      where: { id },
      include: {
        author: {
          select: {
            name: true,
            avatar: true,
          },
        },
      },
    })

    if (!article) return null

    return {
      id: article.id,
      title: article.title,
      slug: article.slug,
      excerpt: article.excerpt,
      content: article.content.split("\n\n"),
      image: article.image || "/placeholder.svg?height=600&width=800",
      date: article.date.toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
      }),
      readTime: article.readTime,
      category: article.category,
      author: {
        name: article.author.name,
        avatar: article.author.avatar || "/placeholder.svg?height=100&width=100",
      },
    }
  } catch (error) {
    console.error("Error fetching article:", error)
    return null
  }
}

export async function createArticle(formData: FormData) {
  // Check if user is admin
  const userIsAdmin = await isAdmin()
  if (!userIsAdmin) {
    return { success: false, message: "Unauthorized" }
  }

  const user = await getCurrentUser()
  if (!user) {
    return { success: false, message: "User not found" }
  }

  const title = formData.get("title") as string
  const excerpt = formData.get("excerpt") as string
  const content = formData.get("content") as string
  const image = formData.get("image") as string
  const readTime = Number.parseInt(formData.get("readTime") as string) || 5
  const category = formData.get("category") as string

  if (!title || !excerpt || !content || !category) {
    return { success: false, message: "Required fields are missing" }
  }

  try {
    const slug = slugify(title)

    // Check if slug already exists
    const existingArticle = await prisma.article.findUnique({
      where: { slug },
    })

    if (existingArticle) {
      return { success: false, message: "An article with a similar title already exists" }
    }

    const newArticle = await prisma.article.create({
      data: {
        title,
        slug,
        excerpt,
        content,
        image: image || "/placeholder.svg?height=600&width=800",
        readTime,
        category,
        authorId: user.id,
      },
    })

    // Revalidate paths to update the UI
    revalidatePath("/")
    revalidatePath("/articles")
    revalidatePath("/categories")
    revalidatePath("/about")

    return { success: true, message: "Article created successfully", article: newArticle }
  } catch (error) {
    console.error("Error creating article:", error)
    return { success: false, message: "Failed to create article" }
  }
}

export async function updateArticleAction(id: string, formData: FormData) {
  // Check if user is admin
  const userIsAdmin = await isAdmin()
  if (!userIsAdmin) {
    return { success: false, message: "Unauthorized" }
  }

  const title = formData.get("title") as string
  const excerpt = formData.get("excerpt") as string
  const content = formData.get("content") as string
  const image = formData.get("image") as string
  const readTime = Number.parseInt(formData.get("readTime") as string) || 5
  const category = formData.get("category") as string

  if (!title || !excerpt || !content || !category) {
    return { success: false, message: "Required fields are missing" }
  }

  try {
    const slug = slugify(title)

    // Check if slug already exists for another article
    const existingArticle = await prisma.article.findFirst({
      where: {
        slug,
        NOT: { id },
      },
    })

    if (existingArticle) {
      return { success: false, message: "An article with a similar title already exists" }
    }

    const updatedArticle = await prisma.article.update({
      where: { id },
      data: {
        title,
        slug,
        excerpt,
        content,
        image: image || "/placeholder.svg?height=600&width=800",
        readTime,
        category,
      },
    })

    if (!updatedArticle) {
      return { success: false, message: "Article not found" }
    }

    // Revalidate paths to update the UI
    revalidatePath("/")
    revalidatePath("/articles")
    revalidatePath("/categories")
    revalidatePath(`/articles/${id}`)
    revalidatePath("/about")

    return { success: true, message: "Article updated successfully", article: updatedArticle }
  } catch (error) {
    console.error("Error updating article:", error)
    return { success: false, message: "Failed to update article" }
  }
}

export async function deleteArticleAction(id: string) {
  // Check if user is admin
  const userIsAdmin = await isAdmin()
  if (!userIsAdmin) {
    return { success: false, message: "Unauthorized" }
  }

  try {
    const deletedArticle = await prisma.article.delete({
      where: { id },
    })

    if (!deletedArticle) {
      return { success: false, message: "Article not found" }
    }

    // Revalidate paths to update the UI
    revalidatePath("/")
    revalidatePath("/articles")
    revalidatePath("/categories")
    revalidatePath("/about")

    return { success: true, message: "Article deleted successfully" }
  } catch (error) {
    console.error("Error deleting article:", error)
    return { success: false, message: "Failed to delete article" }
  }
}

export async function getUniqueAuthors() {
  try {
    const authors = await prisma.user.findMany({
      select: {
        id: true,
        name: true,
        avatar: true,
        _count: {
          select: { articles: true },
        },
      },
      where: {
        articles: {
          some: {},
        },
      },
    })

    return authors.map((author) => ({
      name: author.name,
      avatar: author.avatar || "/placeholder.svg?height=100&width=100",
      articleCount: author._count.articles,
    }))
  } catch (error) {
    console.error("Error fetching authors:", error)
    return []
  }
}

