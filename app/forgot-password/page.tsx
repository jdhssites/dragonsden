"use client"

import type React from "react"

import { useState } from "react"
import Link from "next/link"

import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from "@/components/ui/card"
import { resetPassword } from "@/lib/auth"

export default function ForgotPasswordPage() {
  const [isLoading, setIsLoading] = useState(false)
  const [message, setMessage] = useState("")
  const [email, setEmail] = useState("")

  async function onSubmit(event: React.FormEvent<HTMLFormElement>) {
    event.preventDefault()
    setIsLoading(true)

    const result = await resetPassword(email)

    setIsLoading(false)
    setMessage(result.message)
  }

  return (
    <div className="container max-w-md mx-auto px-4 py-16">
      <Card>
        <CardHeader className="space-y-1">
          <CardTitle className="text-2xl font-bold">Reset password</CardTitle>
          <CardDescription>Enter your email address and we'll send you a link to reset your password</CardDescription>
        </CardHeader>
        <form onSubmit={onSubmit}>
          <CardContent className="space-y-4">
            {message && <div className="bg-primary/15 text-primary text-sm p-3 rounded-md">{message}</div>}
            <div className="space-y-2">
              <Label htmlFor="email">Email</Label>
              <Input
                id="email"
                name="email"
                type="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                required
              />
            </div>
          </CardContent>
          <CardFooter className="flex flex-col space-y-4">
            <Button type="submit" className="w-full" disabled={isLoading}>
              {isLoading ? "Sending reset link..." : "Send reset link"}
            </Button>
            <div className="text-sm text-center text-muted-foreground">
              Remember your password?{" "}
              <Link href="/login" className="text-primary underline underline-offset-4 hover:text-primary/90">
                Back to login
              </Link>
            </div>
          </CardFooter>
        </form>
      </Card>
    </div>
  )
}

