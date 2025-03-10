"use client"

import { useState } from "react"
import { useRouter } from "next/navigation"

import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from "@/components/ui/card"
import { changePassword } from "@/lib/auth"

export default function SecurityForm() {
  const router = useRouter()
  const [isSubmitting, setIsSubmitting] = useState(false)
  const [message, setMessage] = useState({ type: "", text: "" })

  async function handleSubmit(formData: FormData) {
    setIsSubmitting(true)
    setMessage({ type: "", text: "" })

    try {
      const result = await changePassword(formData)

      if (result.success) {
        setMessage({ type: "success", text: result.message })
        // Reset form
        const form = document.getElementById("password-form") as HTMLFormElement
        form.reset()
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
        <CardTitle>Security</CardTitle>
        <CardDescription>Change your password</CardDescription>
      </CardHeader>
      <form id="password-form" action={handleSubmit}>
        <CardContent className="space-y-6">
          {message.text && (
            <div
              className={`p-3 rounded-md ${message.type === "success" ? "bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400" : "bg-destructive/15 text-destructive"}`}
            >
              {message.text}
            </div>
          )}

          <div className="space-y-2">
            <Label htmlFor="currentPassword">Current Password</Label>
            <Input id="currentPassword" name="currentPassword" type="password" required />
          </div>

          <div className="space-y-2">
            <Label htmlFor="newPassword">New Password</Label>
            <Input id="newPassword" name="newPassword" type="password" required />
          </div>

          <div className="space-y-2">
            <Label htmlFor="confirmPassword">Confirm New Password</Label>
            <Input id="confirmPassword" name="confirmPassword" type="password" required />
          </div>
        </CardContent>
        <CardFooter>
          <Button type="submit" disabled={isSubmitting}>
            {isSubmitting ? "Changing Password..." : "Change Password"}
          </Button>
        </CardFooter>
      </form>
    </Card>
  )
}

