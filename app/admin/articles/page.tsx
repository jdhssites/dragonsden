import Link from "next/link"
import { redirect } from "next/navigation"
import { Edit, Trash, Plus } from "lucide-react"

import { Button } from "@/components/ui/button"
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"
import { Badge } from "@/components/ui/badge"
import { getArticles } from "@/lib/article-actions"
import { isAdmin } from "@/lib/auth"
import { deleteArticleAction } from "@/lib/article-actions"

export default async function AdminArticlesPage() {
  // Check if user is admin
  const userIsAdmin = await isAdmin()
  if (!userIsAdmin) {
    redirect("/login")
  }

  const articles = await getArticles()

  return (
    <div className="container mx-auto px-4 py-8">
      <div className="flex items-center justify-between mb-8">
        <h1 className="text-4xl font-bold text-primary">Manage Articles</h1>
        <Button asChild>
          <Link href="/admin/articles/new" className="flex items-center gap-2">
            <Plus size={16} />
            <span>New Article</span>
          </Link>
        </Button>
      </div>

      <div className="grid gap-6">
        {articles.map((article) => (
          <Card key={article.id} className="border border-border/60">
            <CardHeader className="pb-3">
              <div className="flex items-start justify-between">
                <div>
                  <CardTitle className="text-xl">{article.title}</CardTitle>
                  <div className="flex items-center gap-2 mt-2">
                    <Badge>{article.category}</Badge>
                    <span className="text-sm text-muted-foreground">{article.date}</span>
                  </div>
                </div>
                <div className="flex items-center gap-2">
                  <Button variant="outline" size="sm" asChild>
                    <Link href={`/admin/articles/${article.id}/edit`} className="flex items-center gap-1">
                      <Edit size={14} />
                      <span>Edit</span>
                    </Link>
                  </Button>
                  <form
                    action={async () => {
                      "use server"
                      await deleteArticleAction(article.id)
                      redirect("/admin/articles")
                    }}
                  >
                    <Button variant="destructive" size="sm" type="submit" className="flex items-center gap-1">
                      <Trash size={14} />
                      <span>Delete</span>
                    </Button>
                  </form>
                </div>
              </div>
            </CardHeader>
            <CardContent>
              <p className="text-muted-foreground line-clamp-2">{article.excerpt}</p>
            </CardContent>
          </Card>
        ))}

        {articles.length === 0 && (
          <div className="text-center py-12">
            <h3 className="text-xl font-medium">No articles found</h3>
            <p className="text-muted-foreground mt-2">Create your first article to get started</p>
            <Button asChild className="mt-4">
              <Link href="/admin/articles/new">Create Article</Link>
            </Button>
          </div>
        )}
      </div>
    </div>
  )
}

