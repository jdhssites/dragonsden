import Image from "next/image"
import Link from "next/link"
import { notFound } from "next/navigation"
import { Calendar, Clock, Tag, ChevronLeft } from "lucide-react"

import { Button } from "@/components/ui/button"
import { Badge } from "@/components/ui/badge"
import { getArticleById, getArticles } from "@/lib/article-actions"
import RelatedArticles from "@/components/related-articles"

export default async function ArticlePage({ params }: { params: { id: string } }) {
  const article = await getArticleById(params.id)

  if (!article) {
    notFound()
  }

  const articles = await getArticles()
  const relatedArticles = articles.filter((a) => a.category === article.category && a.id !== article.id).slice(0, 3)

  return (
    <div className="container mx-auto px-4 py-8">
      <Button variant="ghost" asChild className="mb-6">
        <Link href="/articles" className="flex items-center gap-2">
          <ChevronLeft size={16} />
          Back to articles
        </Link>
      </Button>

      <article className="max-w-3xl mx-auto">
        <Badge className="mb-4">{article.category}</Badge>
        <h1 className="text-4xl font-bold mb-4 leading-tight">{article.title}</h1>

        <div className="flex items-center gap-4 text-muted-foreground mb-8">
          <div className="flex items-center gap-1">
            <Calendar size={16} />
            <span>{article.date}</span>
          </div>
          <div className="flex items-center gap-1">
            <Clock size={16} />
            <span>{article.readTime} min read</span>
          </div>
          <div className="flex items-center gap-1">
            <Tag size={16} />
            <span>{article.category}</span>
          </div>
        </div>

        <div className="relative w-full h-[400px] mb-8 rounded-lg overflow-hidden">
          <Image src={article.image || "/placeholder.svg"} alt={article.title} fill className="object-cover" />
        </div>

        <div className="prose prose-lg max-w-none prose-headings:font-serif prose-headings:font-bold prose-p:text-base prose-p:leading-relaxed">
          {article.content.map((paragraph: string, index: number) => (
            <p key={index} className="mb-4">
              {paragraph}
            </p>
          ))}
        </div>
      </article>

      <div className="max-w-3xl mx-auto mt-16">
        <h2 className="text-2xl font-bold mb-6">Related Articles</h2>
        <RelatedArticles articles={relatedArticles} />
      </div>
    </div>
  )
}

