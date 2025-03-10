"use client"

import { useState } from "react"
import { useRouter } from "next/navigation"
import Link from "next/link"
import Image from "next/image"
import { ChevronLeft } from "lucide-react"

import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Textarea } from "@/components/ui/textarea"
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from "@/components/ui/card"
import { updateArticleAction } from "@/lib/article-actions"
import { useToast } from "@/hooks/use-toast"

// Common placeholder images
const PLACEHOLDER_IMAGES = [
  "/placeholder.svg?height=600&width=800",
  "/placeholder.svg?height=600&width=800&text=Technology",
  "/placeholder.svg?height=600&width=800&text=Health",
  "/placeholder.svg?height=600&width=800&text=Business",
  "/placeholder.svg?height=600&width=800&text=Science",
  "/placeholder.svg?height=600&width=800&text=Lifestyle",
]

interface EditArticleFormProps {
  article: any
}

export default function EditArticleForm({ article }: EditArticleFormProps) {
  const router = useRouter()
  const { toast } = useToast()
  const [isSubmitting, setIsSubmitting] = useState(false)
  const [error, setError] = useState("")
  const [imagePreview, setImagePreview] = useState(article.image)

  async function handleSubmit(formData: FormData) {
    setIsSubmitting(true)
    setError("")

    try {
      const result = await updateArticleAction(article.id, formData)

      if (result.success) {
        toast({
          title: "Success",
          description: "Article updated successfully",
          variant: "default",
        })
        router.push("/admin/articles")
        router.refresh()
      } else {
        setError(result.message)
      }
    } catch (err) {
      setError("An error occurred. Please try again.")
      console.error(err)
    } finally {
      setIsSubmitting(false)
    }
  }

  return (
    <div className="container mx-auto px-4 py-8">
      <Button variant="ghost" asChild className="mb-6">
        <Link href="/admin/articles" className="flex items-center gap-2">
          <ChevronLeft size={16} />
          Back to articles
        </Link>
      </Button>

      <Card className="max-w-4xl mx-auto border border-border/60">
        <CardHeader>
          <CardTitle className="text-2xl">Edit Article</CardTitle>
        </CardHeader>
        <form action={handleSubmit}>
          <CardContent className="space-y-6">
            {error && <div className="bg-destructive/15 text-destructive text-sm p-3 rounded-md">{error}</div>}

            <div className="space-y-2">
              <Label htmlFor="title">Title</Label>
              <Input id="title" name="title" defaultValue={article.title} required />
            </div>

            <div className="space-y-2">
              <Label htmlFor="excerpt">Excerpt</Label>
              <Textarea id="excerpt" name="excerpt" defaultValue={article.excerpt} className="h-20" required />
            </div>

            <div className="space-y-2">
              <Label htmlFor="content">Content</Label>
              <Textarea
                id="content"
                name="content"
                defaultValue={Array.isArray(article.content) ? article.content.join("\n\n") : article.content}
                placeholder="Write your article content here. Separate paragraphs with blank lines."
                className="h-64"
                required
              />
            </div>

            <div className="space-y-4">
              <Label>Image</Label>
              <div className="relative w-full h-48 mb-2 rounded-md overflow-hidden border">
                <Image
                  src={imagePreview || "/placeholder.svg"}
                  alt="Article preview"
                  fill
                  className="object-cover"
                  onError={() => setImagePreview(PLACEHOLDER_IMAGES[0])}
                />
              </div>

              <div className="grid grid-cols-3 gap-2 mb-4">
                {PLACEHOLDER_IMAGES.map((img, index) => (
                  <button
                    key={index}
                    type="button"
                    className={`relative h-20 rounded-md overflow-hidden border ${imagePreview === img ? "ring-2 ring-primary" : ""}`}
                    onClick={() => {
                      setImagePreview(img)
                      const imageInput = document.getElementById("image") as HTMLInputElement
                      if (imageInput) imageInput.value = img
                    }}
                  >
                    <Image
                      src={img || "/placeholder.svg"}
                      alt={`Placeholder ${index + 1}`}
                      fill
                      className="object-cover"
                    />
                  </button>
                ))}
              </div>

              <div className="space-y-2">
                <Label htmlFor="image">Custom Image URL</Label>
                <Input
                  id="image"
                  name="image"
                  defaultValue={article.image}
                  placeholder="/placeholder.svg?height=600&width=800"
                  onChange={(e) => setImagePreview(e.target.value || PLACEHOLDER_IMAGES[0])}
                />
              </div>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div className="space-y-2">
                <Label htmlFor="readTime">Read Time (minutes)</Label>
                <Input
                  id="readTime"
                  name="readTime"
                  type="number"
                  min="1"
                  defaultValue={article.readTime.toString()}
                  required
                />
              </div>

              <div className="space-y-2">
                <Label htmlFor="category">Category</Label>
                <Input
                  id="category"
                  name="category"
                  defaultValue={article.category}
                  placeholder="technology, health, business, etc."
                  required
                />
              </div>
            </div>
          </CardContent>
          <CardFooter className="flex justify-end gap-2">
            <Button variant="outline" asChild>
              <Link href="/admin/articles">Cancel</Link>
            </Button>
            <Button type="submit" disabled={isSubmitting}>
              {isSubmitting ? "Saving..." : "Save Changes"}
            </Button>
          </CardFooter>
        </form>
      </Card>
    </div>
  )
}

