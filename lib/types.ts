export interface Article {
  id: string
  title: string
  excerpt: string
  content: string[]
  image: string
  date: string
  readTime: number
  category: string
  author: {
    name: string
    avatar: string
  }
}

