import type React from "react"
import type { Metadata } from "next"
import localFont from "next/font/local"
import "./globals.css"

import { ThemeProvider } from "@/components/theme-provider"
import Navbar from "@/components/navbar"
import Footer from "@/components/footer"
import { getCurrentUser } from "@/lib/auth"

const timesNewRoman = localFont({
  src: [
    {
      path: "../public/fonts/times-new-roman.ttf",
      weight: "400",
      style: "normal",
    },
    {
      path: "../public/fonts/times-new-roman-bold.ttf",
      weight: "700",
      style: "normal",
    },
    {
      path: "../public/fonts/times-new-roman-italic.ttf",
      weight: "400",
      style: "italic",
    },
  ],
  fallback: ["Times New Roman", "serif"],
})

export const metadata: Metadata = {
  title: "Dragon's Den | Professional News & Insights",
  description:
    "Your trusted source for professional insights and in-depth analysis across business, technology, and more.",
    generator: 'v0.dev'
}

export default async function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode
}>) {
  const user = await getCurrentUser()
  const userTheme = user?.theme || "system"

  return (
    <html lang="en" suppressHydrationWarning>
      <body className={timesNewRoman.className}>
        <ThemeProvider attribute="class" defaultTheme={userTheme} enableSystem disableTransitionOnChange>
          <div className="flex min-h-screen flex-col">
            <Navbar user={user} />
            <main className="flex-1">{children}</main>
            <Footer />
          </div>
        </ThemeProvider>
      </body>
    </html>
  )
}



import './globals.css'