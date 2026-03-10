import { z, defineCollection } from 'astro:content'
import { glob } from 'astro/loaders'

const blog = defineCollection({
	loader: glob({ pattern: '**/*.md', base: './src/content/blog' }),
	schema: () =>
		z.object({
			title: z.string(),
			description: z.string(),
			pubDate: z.date(),
			image: z.string(),
			author: z.string(),
			tags: z.array(z.string()),
			faq: z
				.array(
					z.object({
						question: z.string(),
						answer: z.string()
					})
				)
				.optional()
		})
})

const glossary = defineCollection({
	loader: glob({ pattern: '**/*.md', base: './src/content/glossary' }),
	schema: () =>
		z.object({
			title: z.string(),
			description: z.string(),
			pubDate: z.date()
		})
})

const lighthouse = defineCollection({
	loader: glob({ pattern: '**/*.md', base: './src/content/lighthouse' }),
	schema: () =>
		z.object({
			title: z.string(),
			description: z.string(),
			pubDate: z.date(),
			auditId: z.string(),
			category: z.enum(['performance', 'accessibility', 'seo', 'best-practices']),
			weight: z.number(),
			faq: z
				.array(
					z.object({
						question: z.string(),
						answer: z.string()
					})
				)
				.optional()
		})
})

export const collections = {
	blog,
	glossary,
	lighthouse
}
