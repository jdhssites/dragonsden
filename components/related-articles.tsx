import Link from "next/link"
import Image from "next/image"
import { ArrowRight } from "lucide-react"

import type { Article } from "@/lib/types"

interface RelatedArticlesProps {
  articles: Article[]
}

export default function RelatedArticles({ articles }: RelatedArticlesProps) {
  return (
    <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
      {articles.map((article) => (
        <div key={article.id} className="group">
          <div className="relative h-40 mb-3 overflow-hidden rounded-md">
            <Image
              src={article.image || "/placeholder.svg"}
              alt={article.title}
              fill
              className="object-cover transition-transform group-hover:scale-105"
            />
          </div>
          <Link href={`/articles/${article.id}`}>
            <h3 className="font-medium group-hover:text-primary transition-colors line-clamp-2">{article.title}</h3>
          </Link>
          <div className="mt-2 flex items-center text-sm text-muted-foreground">
            <span>{article.date}</span>
            <span className="mx-2">•</span>
            <span>{article.readTime} min read</span>
          </div>
          <Link
            href={`/articles/${article.id}`}
            className="mt-2 inline-flex items-center gap-1 text-sm text-primary hover:underline"
          >
            Read more <ArrowRight size={14} />
          </Link>
        </div>
      ))}
    </div>
  )
}

