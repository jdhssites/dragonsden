import Image from "next/image"
import Link from "next/link"
import { Calendar, Clock, ArrowRight } from "lucide-react"

import { Button } from "@/components/ui/button"
import { Badge } from "@/components/ui/badge"
import type { Article } from "@/lib/types"

interface FeaturedArticleProps {
  article: Article
}

export default function FeaturedArticle({ article }: FeaturedArticleProps) {
  return (
    <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 bg-secondary/30 rounded-lg overflow-hidden border">
      <div className="relative h-[300px] lg:h-full">
        <Image src={article.image || "/placeholder.svg"} alt={article.title} fill className="object-cover" />
      </div>
      <div className="p-6 flex flex-col justify-center">
        <Badge className="w-fit mb-4">{article.category}</Badge>
        <Link href={`/articles/${article.id}`}>
          <h3 className="text-3xl font-bold mb-4 hover:text-primary transition-colors leading-tight">
            {article.title}
          </h3>
        </Link>
        <p className="text-muted-foreground mb-6">{article.excerpt}</p>
        <div className="flex items-center gap-4 text-sm text-muted-foreground mb-6">
          <div className="flex items-center gap-1">
            <Calendar size={14} />
            <span>{article.date}</span>
          </div>
          <div className="flex items-center gap-1">
            <Clock size={14} />
            <span>{article.readTime} min read</span>
          </div>
        </div>
        <Button asChild className="w-fit bg-primary hover:bg-primary/90">
          <Link href={`/articles/${article.id}`} className="flex items-center gap-2">
            <span>Read Article</span>
            <ArrowRight size={16} />
          </Link>
        </Button>
      </div>
    </div>
  )
}

