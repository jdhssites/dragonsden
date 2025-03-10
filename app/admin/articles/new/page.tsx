"use client"

import { redirect } from "next/navigation"
import Link from "next/link"
import { ChevronLeft } from "lucide-react"

import { Button } from "@/components/ui/button"
import { isAdmin } from "@/lib/auth"
import NewArticleForm from "@/components/new-article-form"

export default async function NewArticlePage() {
  // Check if user is admin
  const userIsAdmin = await isAdmin()
  if (!userIsAdmin) {
    redirect("/login")
  }

  return (
    <div className="container mx-auto px-4 py-8">
      <Button variant="ghost" asChild className="mb-6">
        <Link href="/admin/articles" className="flex items-center gap-2">
          <ChevronLeft size={16} />
          Back to articles
        </Link>
      </Button>

      <NewArticleForm />
    </div>
  )
}

