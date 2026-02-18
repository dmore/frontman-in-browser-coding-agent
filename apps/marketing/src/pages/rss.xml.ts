import rss from '@astrojs/rss'
import type { APIContext } from 'astro'
import { getCollection } from 'astro:content'

export async function GET(context: APIContext) {
	const posts = await getCollection('blog')
	const sortedPosts = posts.sort(
		(a, b) => new Date(b.data.pubDate).valueOf() - new Date(a.data.pubDate).valueOf()
	)

	if (!context.site) {
		throw new Error('Astro `site` config is required for RSS feed generation')
	}

	return rss({
		title: 'Frontman Blog',
		description:
			'The open-source AI agent that lives in your browser, sees your live DOM, and edits your frontend. Updates, tutorials, and insights.',
		site: context.site,
		items: sortedPosts.map((post) => ({
			title: post.data.title,
			pubDate: post.data.pubDate,
			description: post.data.description,
			link: `/blog/${post.id}/`
		})),
		customData: `<language>en-us</language>`
	})
}
