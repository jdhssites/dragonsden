import Link from "next/link"
import { ArrowRight, Clock } from "lucide-react"

import { Button } from "@/components/ui/button"
import FeaturedArticle from "@/components/featured-article"
import ArticleCard from "@/components/article-card"
import { getArticles } from "@/lib/article-actions"

export default async function Home() {
  const articles = await getArticles()
  const featuredArticle = articles[0]
  const recentArticles = articles.slice(1, 7)

  return (
    <div className="container mx-auto px-4 py-12">
      <section className="mb-16">
        <div className="flex items-center justify-between mb-6">
          <h2 className="text-3xl font-bold text-primary">Featured Article</h2>
          <Button variant="ghost" asChild>
            <Link href="/articles" className="flex items-center gap-2">
              <span>View all</span>
              <ArrowRight size={16} />
            </Link>
          </Button>
        </div>
        {featuredArticle && <FeaturedArticle article={featuredArticle} />}
        {!featuredArticle && (
          <div className="text-center py-12 bg-secondary/30 rounded-lg border">
            <h3 className="text-xl font-medium">No featured article available</h3>
            <p className="text-muted-foreground mt-2">Check back soon for new content</p>
          </div>
        )}
      </section>

      <section>
        <div className="flex items-center justify-between mb-6">
          <h2 className="text-3xl font-bold text-primary">Recent Articles</h2>
          <div className="flex items-center gap-2 text-muted-foreground">
            <Clock size={16} />
            <span>Updated daily</span>
          </div>
        </div>
        {recentArticles.length > 0 ? (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {recentArticles.map((article) => (
              <ArticleCard key={article.id} article={article} />
            ))}
          </div>
        ) : (
          <div className="text-center py-12 bg-secondary/30 rounded-lg border">
            <h3 className="text-xl font-medium">No recent articles available</h3>
            <p className="text-muted-foreground mt-2">Check back soon for new content</p>
          </div>
        )}
      </section>
    </div>
  )
}

