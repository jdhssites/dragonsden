"use client"

import { useState } from "react"
import { useRouter } from "next/navigation"

import { Button } from "@/components/ui/button"
import { Label } from "@/components/ui/label"
import { Checkbox } from "@/components/ui/checkbox"
import { RadioGroup, RadioGroupItem } from "@/components/ui/radio-group"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from "@/components/ui/card"
import { updateUserProfile } from "@/lib/auth"
import type { User } from "@/lib/auth"

interface PreferencesFormProps {
  user: User
}

export default function PreferencesForm({ user }: PreferencesFormProps) {
  const router = useRouter()
  const [isSubmitting, setIsSubmitting] = useState(false)
  const [message, setMessage] = useState({ type: "", text: "" })
  const [theme, setTheme] = useState(user.theme || "system")
  const [language, setLanguage] = useState(user.preferences?.language || "en")

  async function handleSubmit(formData: FormData) {
    // Add the theme and language to the form data
    formData.append("theme", theme)
    formData.append("language", language)

    // Add required name and email fields from user object
    formData.append("name", user.name)
    formData.append("email", user.email)

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
        <CardTitle>Preferences</CardTitle>
        <CardDescription>Customize your experience</CardDescription>
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

          <div className="space-y-4">
            <div>
              <h3 className="text-lg font-medium">Theme</h3>
              <p className="text-sm text-muted-foreground mb-4">Select your preferred theme</p>

              <RadioGroup defaultValue={theme} onValueChange={setTheme} className="flex flex-col space-y-2">
                <div className="flex items-center space-x-2">
                  <RadioGroupItem value="light" id="theme-light" />
                  <Label htmlFor="theme-light">Light</Label>
                </div>
                <div className="flex items-center space-x-2">
                  <RadioGroupItem value="dark" id="theme-dark" />
                  <Label htmlFor="theme-dark">Dark</Label>
                </div>
                <div className="flex items-center space-x-2">
                  <RadioGroupItem value="system" id="theme-system" />
                  <Label htmlFor="theme-system">System</Label>
                </div>
              </RadioGroup>
            </div>

            <div>
              <h3 className="text-lg font-medium">Notifications</h3>
              <p className="text-sm text-muted-foreground mb-4">Configure your notification preferences</p>

              <div className="flex items-center space-x-2">
                <Checkbox
                  id="emailNotifications"
                  name="emailNotifications"
                  defaultChecked={user.preferences?.emailNotifications}
                />
                <Label htmlFor="emailNotifications">Email notifications</Label>
              </div>
            </div>

            <div>
              <h3 className="text-lg font-medium">Appearance</h3>
              <p className="text-sm text-muted-foreground mb-4">Customize how the site looks</p>

              <div className="flex items-center space-x-2">
                <Checkbox id="darkMode" name="darkMode" defaultChecked={user.preferences?.darkMode} />
                <Label htmlFor="darkMode">Enable dark mode</Label>
              </div>
            </div>

            <div>
              <h3 className="text-lg font-medium">Language</h3>
              <p className="text-sm text-muted-foreground mb-4">Choose your preferred language</p>

              <Select defaultValue={language} onValueChange={setLanguage}>
                <SelectTrigger className="w-full md:w-[200px]">
                  <SelectValue placeholder="Select language" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="en">English</SelectItem>
                  <SelectItem value="es">Spanish</SelectItem>
                  <SelectItem value="fr">French</SelectItem>
                  <SelectItem value="de">German</SelectItem>
                  <SelectItem value="zh">Chinese</SelectItem>
                </SelectContent>
              </Select>
            </div>
          </div>
        </CardContent>
        <CardFooter>
          <Button type="submit" disabled={isSubmitting}>
            {isSubmitting ? "Saving..." : "Save Preferences"}
          </Button>
        </CardFooter>
      </form>
    </Card>
  )
}

