import Image from "next/image"
import { Mail, MapPin, Phone } from "lucide-react"

import { Card, CardContent } from "@/components/ui/card"
import { getUniqueAuthors } from "@/lib/data"

export default function AboutPage() {
  const teamMembers = getUniqueAuthors()

  return (
    <div className="container mx-auto px-4 py-8">
      <h1 className="text-4xl font-bold mb-8">About Dragon's Den</h1>

      <section className="mb-16">
        <div className="grid md:grid-cols-2 gap-8 items-center">
          <div>
            <h2 className="text-2xl font-bold mb-4">Our Mission</h2>
            <p className="text-muted-foreground mb-4">
              At Dragon's Den, we believe in the power of knowledge and the importance of accessible, high-quality
              information. Our mission is to provide thoughtful, well-researched articles that inform, inspire, and
              empower our readers.
            </p>
            <p className="text-muted-foreground mb-4">
              Founded in 2023, Dragon's Den has quickly grown to become a trusted source for content across a wide range
              of topics, from technology and science to health, business, and lifestyle.
            </p>
            <p className="text-muted-foreground">
              We are committed to journalistic integrity, factual accuracy, and presenting diverse perspectives that
              help our readers develop a deeper understanding of the world around them.
            </p>
          </div>
          <div className="relative h-[300px] rounded-lg overflow-hidden">
            <Image
              src="/placeholder.svg?height=600&width=800"
              alt="Dragon's Den office"
              fill
              className="object-cover"
            />
          </div>
        </div>
      </section>

      {teamMembers.length > 0 && (
        <section className="mb-16">
          <h2 className="text-2xl font-bold mb-8">Our Team</h2>
          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            {teamMembers.map((member, index) => (
              <Card key={`team-${index}`} className="overflow-hidden">
                <div className="relative h-48 w-full">
                  <Image src={member.avatar || "/placeholder.svg"} alt={member.name} fill className="object-cover" />
                </div>
                <CardContent className="p-4">
                  <h3 className="font-bold">{member.name}</h3>
                  <p className="text-sm text-primary mb-2">
                    {index === 0
                      ? "Founder & Editor-in-Chief"
                      : index === 1
                        ? "Senior Editor"
                        : index === 2
                          ? "Content Manager"
                          : "Contributing Writer"}
                  </p>
                  <p className="text-sm text-muted-foreground">
                    {member.name} brings expertise and insight to our articles, focusing on delivering quality content
                    to our readers.
                  </p>
                </CardContent>
              </Card>
            ))}
          </div>
        </section>
      )}

      <section>
        <h2 className="text-2xl font-bold mb-8">Contact Us</h2>
        <div className="grid md:grid-cols-3 gap-6">
          <Card>
            <CardContent className="p-6 flex flex-col items-center text-center">
              <Mail className="h-10 w-10 text-primary mb-4" />
              <h3 className="font-bold mb-2">Email</h3>
              <p className="text-muted-foreground">contact@dragonsden.com</p>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="p-6 flex flex-col items-center text-center">
              <Phone className="h-10 w-10 text-primary mb-4" />
              <h3 className="font-bold mb-2">Phone</h3>
              <p className="text-muted-foreground">(555) 123-4567</p>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="p-6 flex flex-col items-center text-center">
              <MapPin className="h-10 w-10 text-primary mb-4" />
              <h3 className="font-bold mb-2">Address</h3>
              <p className="text-muted-foreground">
                123 Dragon Street
                <br />
                San Francisco, CA 94103
              </p>
            </CardContent>
          </Card>
        </div>
      </section>
    </div>
  )
}

