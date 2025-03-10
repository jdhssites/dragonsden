"use client"

import Link from "next/link"
import { useRouter } from "next/navigation"
import { LogIn, LogOut, User, UserPlus, FileText, Plus } from "lucide-react"

import { Button } from "@/components/ui/button"
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu"
import { Avatar, AvatarFallback } from "@/components/ui/avatar"
import { logoutUser } from "@/lib/auth"

interface UserMenuProps {
  user: { name: string; email: string; isAdmin?: boolean } | null
}

export function UserMenu({ user }: UserMenuProps) {
  const router = useRouter()

  const handleLogout = async () => {
    await logoutUser()
    router.refresh()
  }

  if (!user) {
    return (
      <div className="flex items-center gap-2">
        <Button variant="ghost" size="sm" asChild>
          <Link href="/login" className="flex items-center gap-2">
            <LogIn className="h-4 w-4" />
            <span>Login</span>
          </Link>
        </Button>
        <Button size="sm" asChild>
          <Link href="/register" className="flex items-center gap-2">
            <UserPlus className="h-4 w-4" />
            <span>Register</span>
          </Link>
        </Button>
      </div>
    )
  }

  return (
    <div className="flex items-center gap-2">
      {user.isAdmin && (
        <Button variant="outline" size="sm" asChild>
          <Link href="/admin/articles/new" className="flex items-center gap-2">
            <Plus className="h-4 w-4" />
            <span>New Article</span>
          </Link>
        </Button>
      )}

      <DropdownMenu>
        <DropdownMenuTrigger asChild>
          <Button variant="ghost" className="relative h-8 w-8 rounded-full">
            <Avatar className="h-8 w-8">
              <AvatarFallback className="bg-primary text-primary-foreground">
                {user.name.charAt(0).toUpperCase()}
              </AvatarFallback>
            </Avatar>
          </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end" className="w-56">
          <div className="flex items-center justify-start gap-2 p-2">
            <div className="flex flex-col space-y-1 leading-none">
              <p className="font-medium">{user.name}</p>
              <p className="text-sm text-muted-foreground">{user.email}</p>
              {user.isAdmin && <p className="text-xs text-primary">Administrator</p>}
            </div>
          </div>
          <DropdownMenuSeparator />
          <DropdownMenuItem asChild>
            <Link href="/profile">
              <User className="mr-2 h-4 w-4" />
              <span>Profile</span>
            </Link>
          </DropdownMenuItem>
          {user.isAdmin && (
            <DropdownMenuItem asChild>
              <Link href="/admin/articles">
                <FileText className="mr-2 h-4 w-4" />
                <span>Manage Articles</span>
              </Link>
            </DropdownMenuItem>
          )}
          <DropdownMenuSeparator />
          <DropdownMenuItem className="text-destructive focus:text-destructive cursor-pointer" onSelect={handleLogout}>
            <LogOut className="mr-2 h-4 w-4" />
            <span>Log out</span>
          </DropdownMenuItem>
        </DropdownMenuContent>
      </DropdownMenu>
    </div>
  )
}

