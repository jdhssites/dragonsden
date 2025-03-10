import Image from "next/image"
import Link from "next/link"
import { Calendar, Clock } from "lucide-react"

import { Card, CardContent, CardFooter } from "@/components/ui/card"
import { Badge } from "@/components/ui/badge"
import type { Article } from "@/lib/types"

interface ArticleCardProps {
  article: Article
}

export default function ArticleCard({ article }: ArticleCardProps) {
  return (
    <Link href={`/articles/${article.id}`} className="block h-full">
      <Card className="overflow-hidden h-full flex flex-col transition-all hover:shadow-md border border-border/60">
        <div className="relative h-48 w-full">
          <Image src={article.image || "/placeholder.svg"} alt={article.title} fill className="object-cover" />
          <Badge className="absolute top-3 left-3">{article.category}</Badge>
        </div>
        <CardContent className="pt-6 flex-1">
          <h3 className="text-xl font-bold mb-2 hover:text-primary transition-colors leading-tight">{article.title}</h3>
          <p className="text-muted-foreground line-clamp-3">{article.excerpt}</p>
        </CardContent>
        <CardFooter className="text-sm text-muted-foreground flex justify-between border-t pt-4">
          <div className="flex items-center gap-1">
            <Calendar size={14} />
            <span>{article.date}</span>
          </div>
          <div className="flex items-center gap-1">
            <Clock size={14} />
            <span>{article.readTime} min read</span>
          </div>
        </CardFooter>
      </Card>
    </Link>
  )
}

