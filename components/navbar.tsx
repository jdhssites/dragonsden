"use client"

import Link from "next/link"
import { useState } from "react"
import { Menu, X } from "lucide-react"

import { Button } from "@/components/ui/button"
import { ThemeToggle } from "@/components/theme-toggle"
import { useMobile } from "@/hooks/use-mobile"
import { UserMenu } from "@/components/user-menu"

interface NavbarProps {
  user: { name: string; email: string } | null
}

export default function Navbar({ user }: NavbarProps) {
  const isMobile = useMobile()
  const [isMenuOpen, setIsMenuOpen] = useState(false)

  const toggleMenu = () => setIsMenuOpen(!isMenuOpen)

  const navItems = [
    { label: "Home", href: "/" },
    { label: "Articles", href: "/articles" },
    { label: "Categories", href: "/categories" },
    { label: "About", href: "/about" },
  ]

  return (
    <header className="border-b">
      <div className="container mx-auto px-4 py-4">
        <div className="flex items-center justify-between">
          <Link href="/" className="flex items-center">
            <span className="text-2xl font-bold tracking-tight text-primary">Dragon's Den</span>
          </Link>

          {isMobile ? (
            <>
              <div className="flex items-center gap-2">
                <ThemeToggle />
                <Button variant="ghost" size="icon" onClick={toggleMenu}>
                  {isMenuOpen ? <X /> : <Menu />}
                </Button>
              </div>
              {isMenuOpen && (
                <div className="fixed inset-0 top-[65px] z-50 bg-background border-t p-4 shadow-lg">
                  <nav className="flex flex-col gap-4">
                    {navItems.map((item) => (
                      <Button key={item.label} variant="ghost" className="justify-start text-lg" asChild>
                        <Link href={item.href} onClick={toggleMenu}>
                          {item.label}
                        </Link>
                      </Button>
                    ))}
                    {!user && (
                      <>
                        <Button variant="ghost" className="justify-start text-lg" asChild>
                          <Link href="/login" onClick={toggleMenu}>
                            Login
                          </Link>
                        </Button>
                        <Button variant="ghost" className="justify-start text-lg" asChild>
                          <Link href="/register" onClick={toggleMenu}>
                            Register
                          </Link>
                        </Button>
                      </>
                    )}
                    {user && (
                      <Button variant="ghost" className="justify-start text-lg" onClick={toggleMenu} asChild>
                        <Link href="/profile">Profile</Link>
                      </Button>
                    )}
                  </nav>
                </div>
              )}
            </>
          ) : (
            <div className="flex items-center gap-6">
              <nav className="flex items-center gap-8">
                {navItems.map((item) => (
                  <Link
                    key={item.label}
                    href={item.href}
                    className="text-base font-medium text-muted-foreground hover:text-primary transition-colors"
                  >
                    {item.label}
                  </Link>
                ))}
              </nav>
              <div className="flex items-center gap-4">
                <ThemeToggle />
                <UserMenu user={user} />
              </div>
            </div>
          )}
        </div>
      </div>
    </header>
  )
}

