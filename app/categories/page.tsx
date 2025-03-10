import Link from "next/link"
import { ChevronRight } from "lucide-react"

import { Badge } from "@/components/ui/badge"
import { Card, CardContent } from "@/components/ui/card"
import { getArticles } from "@/lib/data"

export default function CategoriesPage() {
  const articles = getArticles()

  // Group articles by category
  const categories = articles.reduce(
    (acc, article) => {
      if (!acc[article.category]) {
        acc[article.category] = []
      }
      acc[article.category].push(article)
      return acc
    },
    {} as Record<string, typeof articles>,
  )

  // Sort categories alphabetically
  const sortedCategories = Object.keys(categories).sort()

  return (
    <div className="container mx-auto px-4 py-8">
      <h1 className="text-4xl font-bold mb-8">Categories</h1>

      <div className="grid gap-8">
        {sortedCategories.map((category) => (
          <section key={category} className="space-y-4">
            <div className="flex items-center gap-2">
              <Badge className="px-3 py-1 text-base capitalize">{category}</Badge>
              <span className="text-muted-foreground">({categories[category].length} articles)</span>
            </div>

            <Card>
              <CardContent className="p-6">
                <ul className="divide-y">
                  {categories[category].map((article) => (
                    <li key={article.id} className="py-4 first:pt-0 last:pb-0">
                      <Link href={`/articles/${article.id}`} className="flex items-center justify-between group">
                        <div className="space-y-1">
                          <h3 className="font-medium group-hover:text-primary transition-colors">{article.title}</h3>
                          <p className="text-sm text-muted-foreground line-clamp-1">{article.excerpt}</p>
                        </div>
                        <ChevronRight className="h-5 w-5 text-muted-foreground group-hover:text-primary transition-colors" />
                      </Link>
                    </li>
                  ))}
                </ul>
              </CardContent>
            </Card>
          </section>
        ))}
      </div>
    </div>
  )
}

