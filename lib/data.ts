import type { Article } from "./types"

// Create a module-level variable to store articles
// In a real app, this would be stored in a database
const articlesStore: Article[] = [
  {
    id: "1",
    title: "The Future of Artificial Intelligence in Healthcare",
    excerpt: "Exploring how AI is revolutionizing medical diagnostics, treatment plans, and patient care.",
    content: [
      "Artificial Intelligence (AI) is rapidly transforming the healthcare industry, offering unprecedented opportunities to improve patient outcomes, reduce costs, and enhance the efficiency of medical services.",
      "One of the most promising applications of AI in healthcare is in medical diagnostics. Machine learning algorithms can analyze medical images such as X-rays, MRIs, and CT scans with remarkable accuracy, often detecting subtle abnormalities that might be missed by human radiologists. For example, AI systems have demonstrated the ability to identify early signs of diseases like cancer, allowing for earlier intervention and potentially saving lives.",
      "AI is also making significant contributions to personalized medicine. By analyzing vast amounts of patient data, including genetic information, medical history, and lifestyle factors, AI can help physicians develop tailored treatment plans that are optimized for individual patients. This approach not only improves treatment efficacy but also minimizes adverse effects by avoiding unnecessary medications or procedures.",
      "In addition to diagnostics and treatment planning, AI is enhancing patient care through virtual health assistants and monitoring systems. AI-powered chatbots can provide immediate responses to patient inquiries, schedule appointments, and even offer basic medical advice. Meanwhile, remote monitoring devices equipped with AI can track patients' vital signs and alert healthcare providers to potential issues before they become serious problems.",
      "Despite these promising developments, the integration of AI into healthcare faces several challenges. Concerns about data privacy, algorithm bias, and the potential for over-reliance on technology must be addressed. Additionally, healthcare professionals need proper training to effectively collaborate with AI systems rather than viewing them as replacements.",
      "As we look to the future, the role of AI in healthcare will likely continue to expand. Researchers are exploring applications in drug discovery, surgical robotics, and predictive analytics for population health management. While AI will never replace the human touch in healthcare, it has the potential to be a powerful tool that enhances the capabilities of medical professionals and improves outcomes for patients worldwide.",
    ],
    image: "/placeholder.svg?height=600&width=800",
    date: "March 15, 2023",
    readTime: 8,
    category: "technology",
    author: {
      name: "Dr. Sarah Johnson",
      avatar: "/placeholder.svg?height=100&width=100",
    },
  },
  {
    id: "2",
    title: "Sustainable Living: Small Changes with Big Impact",
    excerpt: "Practical tips for reducing your carbon footprint and living more sustainably in everyday life.",
    content: [
      "As climate change continues to pose significant challenges to our planet, many individuals are seeking ways to reduce their environmental impact through sustainable living practices. The good news is that even small changes in our daily habits can collectively make a substantial difference.",
      "One of the most effective ways to live more sustainably is to reduce energy consumption at home. Simple actions like switching to LED light bulbs, unplugging electronics when not in use, and properly insulating your home can significantly decrease your energy usage. Installing a programmable thermostat can also help by automatically adjusting temperatures when you're away or asleep.",
      "Transportation is another area where sustainable choices can have a major impact. Whenever possible, opt for walking, cycling, or public transportation instead of driving. If you must use a car, consider carpooling or investing in an electric or hybrid vehicle. For unavoidable air travel, look into carbon offset programs to mitigate your flight's emissions.",
      "Our food choices also play a crucial role in sustainability. Reducing meat consumption, especially beef, can dramatically lower your carbon footprint, as livestock production is a significant source of greenhouse gases. Choosing locally grown, seasonal produce reduces the emissions associated with food transportation and supports local farmers. Additionally, planning meals to minimize food waste helps conserve the resources used in food production.",
      "Water conservation is equally important for sustainable living. Installing low-flow faucets and showerheads, fixing leaks promptly, and collecting rainwater for garden irrigation are effective ways to reduce water usage. Even simple habits like turning off the tap while brushing your teeth or taking shorter showers can save thousands of gallons annually.",
      "Finally, embracing the principles of reduce, reuse, and recycle can minimize waste. Before purchasing new items, consider whether you truly need them. When shopping, choose products with minimal packaging and bring your own reusable bags. For items you no longer need, explore options for donating, repurposing, or recycling before discarding them.",
      "By incorporating these sustainable practices into our daily lives, we can collectively work toward a healthier planet for current and future generations. Remember, the journey toward sustainability is not about perfection but progress—every positive change, no matter how small, is a step in the right direction.",
    ],
    image: "/placeholder.svg?height=600&width=800",
    date: "April 22, 2023",
    readTime: 6,
    category: "lifestyle",
    author: {
      name: "Emma Green",
      avatar: "/placeholder.svg?height=100&width=100",
    },
  },
  {
    id: "3",
    title: "The Science of Sleep: Why Quality Rest Matters",
    excerpt: "Understanding the crucial role of sleep in physical health, cognitive function, and emotional wellbeing.",
    content: [
      "Sleep is a fundamental biological process that affects virtually every aspect of our health and wellbeing. Despite spending roughly one-third of our lives asleep, many people underestimate the importance of quality rest and the profound impact it has on our bodies and minds.",
      "During sleep, the body engages in essential maintenance and repair processes. Growth hormone is released, facilitating tissue repair and muscle growth. The immune system produces cytokines, proteins that help fight inflammation and infection. This explains why inadequate sleep is associated with increased susceptibility to illnesses and slower recovery times.",
      "Perhaps even more remarkable is what happens in the brain during sleep. Rather than shutting down, the brain remains highly active, cycling through different sleep stages characterized by distinct patterns of neural activity. During deep sleep, the brain consolidates memories, transferring information from short-term to long-term storage. This process is crucial for learning and skill development.",
      "Rapid Eye Movement (REM) sleep, the stage associated with dreaming, plays a vital role in emotional processing and regulation. During REM sleep, the brain processes emotional experiences and memories, helping us make sense of complex emotions and stressful events. This explains why sleep deprivation often leads to mood disturbances, irritability, and impaired emotional resilience.",
      "The quality of our sleep is influenced by numerous factors, including our sleep environment, daily habits, and overall health. Maintaining a consistent sleep schedule, creating a cool, dark, and quiet sleeping environment, and limiting exposure to screens before bedtime can significantly improve sleep quality. Regular physical activity and mindful stress management also contribute to better sleep.",
      "Despite the clear importance of sleep, modern society often glorifies busyness and productivity at the expense of rest. Many people view sleep as a luxury rather than a necessity, leading to chronic sleep deprivation with serious consequences. Research has linked insufficient sleep to a range of health problems, including cardiovascular disease, diabetes, obesity, and neurodegenerative disorders.",
      "As our understanding of sleep science continues to evolve, one thing remains clear: quality sleep is not indulgent—it's essential. By prioritizing sleep and adopting habits that promote restful nights, we can enhance our physical health, cognitive performance, emotional wellbeing, and overall quality of life.",
    ],
    image: "/placeholder.svg?height=600&width=800",
    date: "May 10, 2023",
    readTime: 7,
    category: "health",
    author: {
      name: "Dr. Michael Chen",
      avatar: "/placeholder.svg?height=100&width=100",
    },
  },
  {
    id: "4",
    title: "Cryptocurrency: Beyond the Hype",
    excerpt: "A balanced look at the potential and pitfalls of cryptocurrency as an investment and technology.",
    content: [
      "Cryptocurrency has evolved from an obscure digital experiment to a global financial phenomenon that has captured the attention of investors, technologists, and policymakers alike. Beyond the headlines about overnight millionaires and dramatic market crashes lies a complex innovation with significant implications for the future of finance and technology.",
      "At its core, cryptocurrency is built on blockchain technology—a distributed ledger system that enables secure, transparent, and immutable record-keeping without centralized control. This fundamental innovation has applications far beyond digital currencies, including supply chain management, voting systems, and digital identity verification.",
      "As an investment, cryptocurrencies offer unique characteristics that distinguish them from traditional assets. They provide potential portfolio diversification due to their historically low correlation with stocks and bonds. Cryptocurrencies also offer accessibility to financial markets for individuals in regions with limited banking infrastructure, potentially democratizing access to investment opportunities.",
      "However, cryptocurrency investments come with substantial risks that potential investors must carefully consider. Extreme price volatility can lead to significant gains but also devastating losses. Regulatory uncertainty remains a major concern, as governments worldwide grapple with how to classify and regulate these novel assets. Security vulnerabilities, from exchange hacks to scams, pose additional risks to cryptocurrency holders.",
      "Beyond investment considerations, cryptocurrencies raise important questions about the future of money and financial systems. Central banks around the world are exploring the development of central bank digital currencies (CBDCs) in response to the rise of private cryptocurrencies. These government-backed digital currencies could potentially combine the efficiency of cryptocurrency technology with the stability and legitimacy of traditional fiat currencies.",
      "Environmental concerns represent another significant aspect of the cryptocurrency debate. Bitcoin and other cryptocurrencies that use proof-of-work consensus mechanisms require enormous amounts of computational power and electricity. Critics argue that this energy consumption contributes significantly to carbon emissions and climate change. In response, some newer cryptocurrencies have adopted alternative consensus mechanisms like proof-of-stake, which require substantially less energy.",
      "As the cryptocurrency ecosystem matures, we're seeing increased institutional adoption and integration with traditional financial systems. Major companies now hold Bitcoin on their balance sheets, payment processors accept cryptocurrency transactions, and traditional banks offer cryptocurrency custody services. This growing mainstream acceptance suggests that cryptocurrencies, in some form, are likely to remain a part of the global financial landscape.",
      "For individuals considering cryptocurrency involvement, education is essential. Understanding the technology, recognizing the risks, and maintaining a balanced perspective can help navigate this complex and rapidly evolving space. Whether cryptocurrencies ultimately transform global finance or settle into a more modest role, the underlying blockchain technology has already sparked innovation across numerous industries and will continue to shape our digital future.",
    ],
    image: "/placeholder.svg?height=600&width=800",
    date: "June 5, 2023",
    readTime: 9,
    category: "finance",
    author: {
      name: "Alex Rivera",
      avatar: "/placeholder.svg?height=100&width=100",
    },
  },
  {
    id: "5",
    title: "The Rise of Remote Work: Reshaping the Modern Workplace",
    excerpt: "How the shift to remote work is changing corporate culture, productivity, and work-life balance.",
    content: [
      "The COVID-19 pandemic accelerated a workplace transformation that had been gradually building for years: the widespread adoption of remote work. What began as a temporary emergency measure has evolved into a fundamental rethinking of how, where, and when work happens. As we move forward, the implications of this shift continue to unfold across organizations, individuals, and society at large.",
      "For companies, remote work has challenged traditional management paradigms. Leaders accustomed to visual supervision have had to adapt to managing by outcomes rather than activity. This transition has prompted organizations to clarify goals, improve communication processes, and implement new collaboration tools. Many companies report surprising productivity gains, challenging long-held assumptions about the necessity of physical presence for effective work.",
      "The financial implications of remote work are significant for both employers and employees. Companies can potentially reduce real estate costs, while employees save on commuting expenses and gain flexibility in choosing where to live. This geographic freedom has sparked migration trends away from expensive urban centers to more affordable locations, potentially reshaping housing markets and local economies.",
      "Technology has been the crucial enabler of remote work, with video conferencing, cloud computing, and digital collaboration tools becoming essential components of the modern workplace. The rapid adoption of these technologies has compressed years of digital transformation into months, forcing organizations to accelerate their technological capabilities and security measures.",
      "Despite its benefits, remote work presents significant challenges. Many workers report feelings of isolation and difficulty maintaining boundaries between work and personal life. The spontaneous interactions that spark innovation in physical workplaces are harder to replicate virtually. Additionally, remote work can exacerbate existing inequalities, as it's primarily accessible to knowledge workers while many essential jobs require physical presence.",
      "As organizations plan for the future, many are adopting hybrid models that combine remote and in-office work. This approach aims to capture the flexibility and efficiency of remote work while preserving the collaboration and cultural benefits of in-person interaction. Successfully implementing hybrid work requires thoughtful policies about scheduling, office design, and inclusive meeting practices to ensure equitable experiences for all employees.",
      "The long-term implications of remote work extend beyond individual companies to broader social structures. From urban planning and transportation systems to childcare arrangements and work visa policies, many aspects of our society were built around the assumption of commuting to physical workplaces. As remote work becomes a permanent feature of our economy, these systems may need to evolve to accommodate new patterns of living and working.",
      "The rise of remote work represents not just a change in location but a fundamental reimagining of the relationship between employers and employees. Organizations that thoughtfully adapt to this new reality—balancing flexibility with connection, autonomy with accountability—will be well-positioned to attract talent and thrive in the evolving workplace landscape.",
    ],
    image: "/placeholder.svg?height=600&width=800",
    date: "July 18, 2023",
    readTime: 8,
    category: "business",
    author: {
      name: "Sophia Williams",
      avatar: "/placeholder.svg?height=100&width=100",
    },
  },
  {
    id: "6",
    title: "The Psychology of Habit Formation",
    excerpt: "Understanding how habits form and practical strategies for building positive routines.",
    content: [
      "Habits shape our lives in profound ways, often operating below the level of conscious awareness. From the moment we wake up until we go to sleep, our days are filled with habitual behaviors—some beneficial, others detrimental. Understanding the psychology behind habit formation can empower us to take control of these automatic behaviors and cultivate routines that support our goals and wellbeing.",
      "At the neurological level, habits form through a process called chunking, where the brain converts a sequence of actions into an automatic routine. This process involves three key components: the cue (trigger that initiates the behavior), the routine (the behavior itself), and the reward (the benefit that reinforces the habit). This cue-routine-reward loop, sometimes called the 'habit loop,' is the foundation of all habit formation.",
      "The brain's preference for habits is rooted in efficiency. By automating routine behaviors, the brain conserves mental energy for more complex tasks. This explains why we can drive a familiar route while deep in thought, or brush our teeth while planning our day. However, this efficiency comes with a trade-off: once established, habits become remarkably persistent and difficult to change.",
      "Research suggests that habit formation typically takes anywhere from 18 to 254 days, with an average of 66 days for a new behavior to become automatic. This timeline varies based on the complexity of the behavior, individual differences, and consistency of practice. Understanding that habit formation is a gradual process can help set realistic expectations and prevent discouragement when change doesn't happen overnight.",
      "Several strategies can facilitate successful habit formation. One powerful approach is habit stacking, which involves linking a new habit to an existing one. For example, if you already have a habit of brewing morning coffee, you might stack a new habit of stretching or meditation while the coffee brews. This leverages the established neural pathway of the existing habit to help encode the new behavior.",
      "Environment design is another crucial factor in habit formation. By structuring our physical spaces to make desired behaviors easier and unwanted behaviors more difficult, we can reduce the friction associated with positive habits. This might involve placing a water bottle on your desk to encourage hydration or keeping smartphones out of the bedroom to improve sleep habits.",
      "Perhaps most importantly, sustainable habit formation requires alignment with personal values and identity. Framing habits in terms of who you wish to become ('I am a person who exercises regularly') rather than what you wish to achieve ('I want to lose weight') creates a powerful internal motivation that can sustain behavior change even when external motivation wanes.",
      "As we navigate the challenge of building better habits, self-compassion is essential. Lapses are an inevitable part of the process, not signs of failure. By approaching habit formation with curiosity rather than judgment, celebrating small wins, and persistently returning to our intentions after setbacks, we can gradually reshape our automatic behaviors to create lives that better reflect our deepest values and aspirations.",
    ],
    image: "/placeholder.svg?height=600&width=800",
    date: "August 3, 2023",
    readTime: 7,
    category: "psychology",
    author: {
      name: "Dr. James Wilson",
      avatar: "/placeholder.svg?height=100&width=100",
    },
  },
  {
    id: "7",
    title: "Exploring Quantum Computing: The Next Technological Frontier",
    excerpt: "How quantum computing works and its potential to revolutionize fields from medicine to cryptography.",
    content: [
      "Quantum computing represents one of the most exciting technological frontiers of our time, promising computational capabilities that far exceed those of classical computers for certain types of problems. While still in its early stages, quantum computing has the potential to transform fields ranging from drug discovery to financial modeling, materials science to artificial intelligence.",
      "Unlike classical computers that use bits (0s and 1s) as their fundamental units of information, quantum computers use quantum bits, or qubits. Qubits leverage two key quantum mechanical properties: superposition and entanglement. Superposition allows qubits to exist in multiple states simultaneously, rather than just 0 or 1. Entanglement creates correlations between qubits, enabling them to share information in ways that have no classical equivalent.",
      "These quantum properties enable quantum computers to process vast amounts of information in parallel, potentially solving certain complex problems exponentially faster than classical computers. For example, Shor's algorithm, a quantum algorithm, could theoretically break widely used encryption systems by factoring large numbers efficiently—a task that would take classical computers billions of years.",
      "One of the most promising applications of quantum computing is in the field of chemistry and materials science. Quantum computers could accurately simulate molecular interactions at a quantum level, potentially revolutionizing drug discovery and the development of new materials. This could lead to breakthroughs in medicine, renewable energy, and industrial processes.",
      "Despite its enormous potential, quantum computing faces significant technical challenges. Qubits are extremely fragile and susceptible to environmental interference, a phenomenon known as decoherence. Maintaining quantum coherence requires sophisticated error correction techniques and often extremely cold temperatures approaching absolute zero. Building large-scale, fault-tolerant quantum computers remains a major engineering challenge.",
      "The current state of quantum computing is often described as the 'NISQ era'—Noisy Intermediate-Scale Quantum—characterized by quantum processors with dozens to hundreds of qubits that are still subject to significant noise and errors. While these systems cannot yet deliver the transformative applications promised by quantum computing, they are valuable for research and development of quantum algorithms and techniques.",
      "As quantum computing advances, it raises important questions about cybersecurity. Many of today's encryption methods could potentially be vulnerable to quantum attacks, prompting research into 'post-quantum cryptography'—encryption techniques designed to be secure against quantum computers. Organizations handling sensitive data with long-term security requirements are already beginning to prepare for this quantum future.",
      "The development of quantum computing is a global race, with major investments from governments, technology companies, and research institutions worldwide. While it's difficult to predict exactly when quantum computers will achieve practical advantages for real-world problems, the field is advancing rapidly. For those interested in the future of technology, quantum computing represents one of the most fascinating areas to watch in the coming decades.",
    ],
    image: "/placeholder.svg?height=600&width=800",
    date: "September 12, 2023",
    readTime: 10,
    category: "technology",
    author: {
      name: "Dr. Robert Chang",
      avatar: "/placeholder.svg?height=100&width=100",
    },
  },
]

// Export a function to get all articles
export function getArticles() {
  return [...articlesStore]
}

// Export a function to get an article by ID
export function getArticleById(id: string) {
  return articlesStore.find((article) => article.id === id)
}

// Export a function to add a new article
export function addArticle(article: Omit<Article, "id" | "date">) {
  const id = Math.random().toString(36).substring(2, 9)
  const date = new Date().toLocaleDateString("en-US", {
    year: "numeric",
    month: "long",
    day: "numeric",
  })

  const newArticle: Article = {
    ...article,
    id,
    date,
  }

  articlesStore.push(newArticle)
  return newArticle
}

// Export a function to update an article
export function updateArticle(id: string, article: Partial<Article>) {
  const index = articlesStore.findIndex((a) => a.id === id)
  if (index !== -1) {
    articlesStore[index] = { ...articlesStore[index], ...article }
    return articlesStore[index]
  }
  return null
}

// Export a function to delete an article
export function deleteArticle(id: string) {
  const index = articlesStore.findIndex((a) => a.id === id)
  if (index !== -1) {
    const deletedArticle = articlesStore[index]
    articlesStore.splice(index, 1)
    return deletedArticle
  }
  return null
}

// Get all unique authors from articles
export function getUniqueAuthors() {
  const authors = articlesStore.map((article) => article.author)
  const uniqueAuthors = []
  const seen = new Set()

  for (const author of authors) {
    if (!seen.has(author.name)) {
      seen.add(author.name)
      uniqueAuthors.push(author)
    }
  }

  return uniqueAuthors
}

// For backward compatibility
export const articles = getArticles()

