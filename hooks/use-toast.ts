"use client"

import { useState } from "react"

interface ToastProps {
  title: string
  description?: string
  variant?: "default" | "destructive"
}

interface Toast extends ToastProps {
  id: string
  visible: boolean
}

let toasts: Toast[] = []
const listeners: ((toasts: Toast[]) => void)[] = []

function notifyListeners() {
  listeners.forEach((listener) => listener([...toasts]))
}

export function toast(props: ToastProps) {
  const id = Math.random().toString(36).substring(2, 9)
  const newToast: Toast = {
    ...props,
    id,
    visible: true,
  }

  toasts.push(newToast)
  notifyListeners()

  // Auto-dismiss after 3 seconds
  setTimeout(() => {
    toasts = toasts.filter((t) => t.id !== id)
    notifyListeners()
  }, 3000)

  return id
}

export function useToast() {
  const [currentToasts, setCurrentToasts] = useState<Toast[]>([])

  if (listeners.length === 0) {
    listeners.push(setCurrentToasts)
  }

  return {
    toasts: currentToasts,
    toast,
  }
}

