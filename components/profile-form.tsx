"use client"

import { useState } from "react"
import { useRouter } from "next/navigation"
import Image from "next/image"
import { User } from "lucide-react"

import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Textarea } from "@/components/ui/textarea"
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from "@/components/ui/card"
import { updateUserProfile } from "@/lib/auth"
import type { User as UserType } from "@/lib/auth"

interface ProfileFormProps {
  user: UserType
}

export default function ProfileForm({ user }: ProfileFormProps) {
  const router = useRouter()
  const [isSubmitting, setIsSubmitting] = useState(false)
  const [message, setMessage] = useState({ type: "", text: "" })
  const [avatarPreview, setAvatarPreview] = useState(user.avatar || "/placeholder.svg?height=100&width=100")

  async function handleSubmit(formData: FormData) {
    setIsSubmitting(true)
    setMessage({ type: "", text: "" })

    try {
      const result = await updateUserProfile(formData)

      if (result.success) {
        setMessage({ type: "success", text: result.message })
        router.refresh()
      } else {
        setMessage({ type: "error", text: result.message })
      }
    } catch (err) {
      setMessage({ type: "error", text: "An error occurred. Please try again." })
      console.error(err)
    } finally {
      setIsSubmitting(false)
    }
  }

  return (
    <Card>
      <CardHeader>
        <CardTitle>Personal Information</CardTitle>
        <CardDescription>Update your personal details</CardDescription>
      </CardHeader>
      <form action={handleSubmit}>
        <CardContent className="space-y-6">
          {message.text && (
            <div
              className={`p-3 rounded-md ${message.type === "success" ? "bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400" : "bg-destructive/15 text-destructive"}`}
            >
              {message.text}
            </div>
          )}

          <div className="flex flex-col md:flex-row gap-6">
            <div className="flex flex-col items-center space-y-2">
              <div className="relative h-24 w-24 rounded-full overflow-hidden border">
                {avatarPreview ? (
                  <Image
                    src={avatarPreview || "/placeholder.svg"}
                    alt={user.name}
                    fill
                    className="object-cover"
                    onError={() => setAvatarPreview("/placeholder.svg?height=100&width=100")}
                  />
                ) : (
                  <div className="h-full w-full flex items-center justify-center bg-muted">
                    <User className="h-12 w-12 text-muted-foreground" />
                  </div>
                )}
              </div>
              <p className="text-xs text-muted-foreground">Profile Picture</p>
            </div>

            <div className="flex-1 space-y-4">
              <div className="space-y-2">
                <Label htmlFor="avatar">Avatar URL</Label>
                <Input
                  id="avatar"
                  name="avatar"
                  defaultValue={user.avatar || ""}
                  placeholder="/placeholder.svg?height=100&width=100"
                  onChange={(e) => setAvatarPreview(e.target.value || "/placeholder.svg?height=100&width=100")}
                />
              </div>

              <div className="space-y-2">
                <Label htmlFor="name">Name</Label>
                <Input id="name" name="name" defaultValue={user.name} required />
              </div>

              <div className="space-y-2">
                <Label htmlFor="email">Email</Label>
                <Input id="email" name="email" type="email" defaultValue={user.email} required />
              </div>

              <div className="space-y-2">
                <Label htmlFor="bio">Bio</Label>
                <Textarea
                  id="bio"
                  name="bio"
                  defaultValue={user.bio || ""}
                  placeholder="Tell us about yourself"
                  className="h-20"
                />
              </div>
            </div>
          </div>
        </CardContent>
        <CardFooter>
          <Button type="submit" disabled={isSubmitting}>
            {isSubmitting ? "Saving..." : "Save Changes"}
          </Button>
        </CardFooter>
      </form>
    </Card>
  )
}

