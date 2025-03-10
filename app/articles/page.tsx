"use client"

import { useState } from "react"
import { Search } from "lucide-react"

import { Input } from "@/components/ui/input"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import ArticleCard from "@/components/article-card"
import { getArticles } from "@/lib/article-actions"
import { useEffect } from "react"

export default function ArticlesPage() {
  const [articles, setArticles] = useState<any[]>([])
  const [searchQuery, setSearchQuery] = useState("")
  const [categoryFilter, setCategoryFilter] = useState("all")
  const [categories, setCategories] = useState<string[]>(["all"])
  const [isLoading, setIsLoading] = useState(true)

  useEffect(() => {
    async function fetchArticles() {
      setIsLoading(true)
      try {
        const fetchedArticles = await getArticles()
        setArticles(fetchedArticles)

        // Extract unique categories
        const uniqueCategories = ["all", ...new Set(fetchedArticles.map((article) => article.category))]
        setCategories(uniqueCategories)
      } catch (error) {
        console.error("Error fetching articles:", error)
      } finally {
        setIsLoading(false)
      }
    }

    fetchArticles()
  }, [])

  const filteredArticles = articles.filter((article) => {
    const matchesSearch =
      article.title.toLowerCase().includes(searchQuery.toLowerCase()) ||
      article.excerpt.toLowerCase().includes(searchQuery.toLowerCase())
    const matchesCategory = categoryFilter === "all" || article.category === categoryFilter

    return matchesSearch && matchesCategory
  })

  return (
    <div className="container mx-auto px-4 py-8">
      <h1 className="text-4xl font-bold mb-8 text-primary">Articles</h1>

      <div className="flex flex-col md:flex-row gap-4 mb-10">
        <div className="relative flex-1">
          <Search className="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
          <Input
            placeholder="Search articles..."
            className="pl-10"
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
          />
        </div>
        <Select value={categoryFilter} onValueChange={setCategoryFilter}>
          <SelectTrigger className="w-full md:w-[180px]">
            <SelectValue placeholder="Category" />
          </SelectTrigger>
          <SelectContent>
            {categories.map((category) => (
              <SelectItem key={category} value={category}>
                {category.charAt(0).toUpperCase() + category.slice(1)}
              </SelectItem>
            ))}
          </SelectContent>
        </Select>
      </div>

      {isLoading ? (
        <div className="text-center py-12">
          <h3 className="text-xl font-medium">Loading articles...</h3>
        </div>
      ) : filteredArticles.length > 0 ? (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {filteredArticles.map((article) => (
            <ArticleCard key={article.id} article={article} />
          ))}
        </div>
      ) : (
        <div className="text-center py-12">
          <h3 className="text-xl font-medium">No articles found</h3>
          <p className="text-muted-foreground mt-2">Try adjusting your search or filter criteria</p>
        </div>
      )}
    </div>
  )
}

