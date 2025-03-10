import { redirect } from "next/navigation"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs"
import { getCurrentUser } from "@/lib/auth"
import ProfileForm from "@/components/profile-form"
import SecurityForm from "@/components/security-form"
import PreferencesForm from "@/components/preferences-form"

export default async function ProfilePage() {
  const user = await getCurrentUser()

  if (!user) {
    redirect("/login")
  }

  return (
    <div className="container max-w-4xl mx-auto px-4 py-8">
      <h1 className="text-4xl font-bold mb-8">Your Profile</h1>

      <Tabs defaultValue="profile" className="w-full">
        <TabsList className="grid w-full grid-cols-3">
          <TabsTrigger value="profile">Profile</TabsTrigger>
          <TabsTrigger value="security">Security</TabsTrigger>
          <TabsTrigger value="preferences">Preferences</TabsTrigger>
        </TabsList>
        <TabsContent value="profile">
          <ProfileForm user={user} />
        </TabsContent>
        <TabsContent value="security">
          <SecurityForm />
        </TabsContent>
        <TabsContent value="preferences">
          <PreferencesForm user={user} />
        </TabsContent>
      </Tabs>
    </div>
  )
}

