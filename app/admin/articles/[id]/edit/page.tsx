import { redirect } from "next/navigation"
import { getArticleById } from "@/lib/article-actions"
import { isAdmin } from "@/lib/auth"
import EditArticleForm from "@/components/edit-article-form"

export default async function EditArticlePage({ params }: { params: { id: string } }) {
  // Check if user is admin
  const userIsAdmin = await isAdmin()
  if (!userIsAdmin) {
    redirect("/login")
  }

  const article = await getArticleById(params.id)

  if (!article) {
    redirect("/admin/articles")
  }

  return <EditArticleForm article={article} />
}

