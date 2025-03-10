import Link from "next/link"
import { FileQuestion } from "lucide-react"

import { Button } from "@/components/ui/button"

export default function NotFound() {
  return (
    <div className="container mx-auto px-4 py-16 flex flex-col items-center justify-center text-center">
      <FileQuestion className="h-24 w-24 text-muted-foreground mb-6" />
      <h1 className="text-4xl font-bold mb-4">Page Not Found</h1>
      <p className="text-muted-foreground mb-8 max-w-md">
        The article or page you're looking for doesn't exist or has been moved.
      </p>
      <Button asChild>
        <Link href="/">Return to Homepage</Link>
      </Button>
    </div>
  )
}

