import { hash } from "bcrypt"
import prisma from "./db"
import { slugify } from "./utils"

export async function seedDatabase() {
  try {
    // Check if we already have users
    const userCount = await prisma.user.count()

    if (userCount === 0) {
      console.log("Seeding database...")

      // Create admin user
      const adminPassword = await hash("admin123", 10)
      const admin = await prisma.user.create({
        data: {
          name: "Admin",
          email: "admin@example.com",
          password: adminPassword,
          isAdmin: true,
          avatar: "/placeholder.svg?height=100&width=100",
          bio: "Site administrator",
        },
      })

      // Sample articles data
      const articlesData = [
        {
          title: "The Future of Artificial Intelligence in Healthcare",
          excerpt: "Exploring how AI is revolutionizing medical diagnostics, treatment plans, and patient care.",
          content: `Artificial Intelligence (AI) is rapidly transforming the healthcare industry, offering unprecedented opportunities to improve patient outcomes, reduce costs, and enhance the efficiency of medical services.

One of the most promising applications of AI in healthcare is in medical diagnostics. Machine learning algorithms can analyze medical images such as X-rays, MRIs, and CT scans with remarkable accuracy, often detecting subtle abnormalities that might be missed by human radiologists. For example, AI systems have demonstrated the ability to identify early signs of diseases like cancer, allowing for earlier intervention and potentially saving lives.

AI is also making significant contributions to personalized medicine. By analyzing vast amounts of patient data, including genetic information, medical history, and lifestyle factors, AI can help physicians develop tailored treatment plans that are optimized for individual patients. This approach not only improves treatment efficacy but also minimizes adverse effects by avoiding unnecessary medications or procedures.

In addition to diagnostics and treatment planning, AI is enhancing patient care through virtual health assistants and monitoring systems. AI-powered chatbots can provide immediate responses to patient inquiries, schedule appointments, and even offer basic medical advice. Meanwhile, remote monitoring devices equipped with AI can track patients' vital signs and alert healthcare providers to potential issues before they become serious problems.

Despite these promising developments, the integration of AI into healthcare faces several challenges. Concerns about data privacy, algorithm bias, and the potential for over-reliance on technology must be addressed. Additionally, healthcare professionals need proper training to effectively collaborate with AI systems rather than viewing them as replacements.

As we look to the future, the role of AI in healthcare will likely continue to expand. Researchers are exploring applications in drug discovery, surgical robotics, and predictive analytics for population health management. While AI will never replace the human touch in healthcare, it has the potential to be a powerful tool that enhances the capabilities of medical professionals and improves outcomes for patients worldwide.`,
          image: "/placeholder.svg?height=600&width=800",
          readTime: 8,
          category: "technology",
          authorId: admin.id,
        },
        {
          title: "Sustainable Living: Small Changes with Big Impact",
          excerpt: "Practical tips for reducing your carbon footprint and living more sustainably in everyday life.",
          content: `As climate change continues to pose significant challenges to our planet, many individuals are seeking ways to reduce their environmental impact through sustainable living practices. The good news is that even small changes in our daily habits can collectively make a substantial difference.

One of the most effective ways to live more sustainably is to reduce energy consumption at home. Simple actions like switching to LED light bulbs, unplugging electronics when not in use, and properly insulating your home can significantly decrease your energy usage. Installing a programmable thermostat can also help by automatically adjusting temperatures when you're away or asleep.

Transportation is another area where sustainable choices can have a major impact. Whenever possible, opt for walking, cycling, or public transportation instead of driving. If you must use a car, consider carpooling or investing in an electric or hybrid vehicle. For unavoidable air travel, look into carbon offset programs to mitigate your flight's emissions.

Our food choices also play a crucial role in sustainability. Reducing meat consumption, especially beef, can dramatically lower your carbon footprint, as livestock production is a significant source of greenhouse gases. Choosing locally grown, seasonal produce reduces the emissions associated with food transportation and supports local farmers. Additionally, planning meals to minimize food waste helps conserve the resources used in food production.

Water conservation is equally important for sustainable living. Installing low-flow faucets and showerheads, fixing leaks promptly, and collecting rainwater for garden irrigation are effective ways to reduce water usage. Even simple habits like turning off the tap while brushing your teeth or taking shorter showers can save thousands of gallons annually.

Finally, embracing the principles of reduce, reuse, and recycle can minimize waste. Before purchasing new items, consider whether you truly need them. When shopping, choose products with minimal packaging and bring your own reusable bags. For items you no longer need, explore options for donating, repurposing, or recycling before discarding them.

By incorporating these sustainable practices into our daily lives, we can collectively work toward a healthier planet for current and future generations. Remember, the journey toward sustainability is not about perfection but progress—every positive change, no matter how small, is a step in the right direction.`,
          image: "/placeholder.svg?height=600&width=800",
          readTime: 6,
          category: "lifestyle",
          authorId: admin.id,
        },
        {
          title: "The Science of Sleep: Why Quality Rest Matters",
          excerpt:
            "Understanding the crucial role of sleep in physical health, cognitive function, and emotional wellbeing.",
          content: `Sleep is a fundamental biological process that affects virtually every aspect of our health and wellbeing. Despite spending roughly one-third of our lives asleep, many people underestimate the importance of quality rest and the profound impact it has on our bodies and minds.

During sleep, the body engages in essential maintenance and repair processes. Growth hormone is released, facilitating tissue repair and muscle growth. The immune system produces cytokines, proteins that help fight inflammation and infection. This explains why inadequate sleep is associated with increased susceptibility to illnesses and slower recovery times.

Perhaps even more remarkable is what happens in the brain during sleep. Rather than shutting down, the brain remains highly active, cycling through different sleep stages characterized by distinct patterns of neural activity. During deep sleep, the brain consolidates memories, transferring information from short-term to long-term storage. This process is crucial for learning and skill development.

Rapid Eye Movement (REM) sleep, the stage associated with dreaming, plays a vital role in emotional processing and regulation. During REM sleep, the brain processes emotional experiences and memories, helping us make sense of complex emotions and stressful events. This explains why sleep deprivation often leads to mood disturbances, irritability, and impaired emotional resilience.

The quality of our sleep is influenced by numerous factors, including our sleep environment, daily habits, and overall health. Maintaining a consistent sleep schedule, creating a cool, dark, and quiet sleeping environment, and limiting exposure to screens before bedtime can significantly improve sleep quality. Regular physical activity and mindful stress management also contribute to better sleep.

Despite the clear importance of sleep, modern society often glorifies busyness and productivity at the expense of rest. Many people view sleep as a luxury rather than a necessity, leading to chronic sleep deprivation with serious consequences. Research has linked insufficient sleep to a range of health problems, including cardiovascular disease, diabetes, obesity, and neurodegenerative disorders.

As our understanding of sleep science continues to evolve, one thing remains clear: quality sleep is not indulgent—it's essential. By prioritizing sleep and adopting habits that promote restful nights, we can enhance our physical health, cognitive performance, emotional wellbeing, and overall quality of life.`,
          image: "/placeholder.svg?height=600&width=800",
          readTime: 7,
          category: "health",
          authorId: admin.id,
        },
      ]

      // Create articles
      for (const articleData of articlesData) {
        await prisma.article.create({
          data: {
            ...articleData,
            slug: slugify(articleData.title),
          },
        })
      }

      console.log("Database seeded successfully!")
    } else {
      console.log("Database already contains data, skipping seed")
    }
  } catch (error) {
    console.error("Error seeding database:", error)
  } finally {
    await prisma.$disconnect()
  }
}

