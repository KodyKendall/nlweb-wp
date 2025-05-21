=== NLWeb for WordPress ===
Contributors: kodykendall
Tags: ai, chatbot, nlweb, microsoft, llm, conversational ai, rss, content
Requires at least: 5.5
Tested up to: 6.5
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2
License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html

Bring AI-powered chat to your WordPress site — using your own content. Let visitors have natural conversations with your blog posts, podcast episodes, and pages.

== Description ==

This plugin exposes your site content through a clean, structured RSS feed compatible with [Microsoft's NLWeb protocol](https://github.com/microsoft/nlweb), and adds a customizable AI chatbot interface directly on your site. Users can now **converse with your blog posts, podcast episodes, and pages** — not just search them.

= ✨ Features =

* **Free** just provide your own OpenAI API Key and NLWeb Server
* **Easy NLWeb Server Setup** One click deploy of an open-source NLWeb compatible server to Railways
* **Auto-generated NLWeb-compatible RSS feed** of your site's content
* **AI chat interface** embedded automatically on your pages, and powered by your content
* Works out of the box with open-source [NLWeb server](https://github.com/microsoft/nlweb)
* Users can **ask natural language questions** and get AI responses grounded in your real content

= 🖼️ Demo =

Visit your domain and ask: "What's the difference between LangChain and LangGraph?"
The chatbot responds with content from your actual blog: "According to our blog post from April 2024: LangGraph is a state machine execution framework for LLM agents..."

= 🛠️ Under the Hood =

* **RSS Feed**: Available at /feed (default for all WordPress sites)
* **Structured metadata**: Uses schema.org for better AI understanding
* **Chat component**: Injects modern chat interface to your frontend
* **Custom Settings Panel**: Configure feed behavior, server URL, styling

= 💡 Use Cases =

* Let visitors ask your blog questions
* Let fans chat with podcast transcripts
* Turn static SEO content into dynamic interactive experiences
* Enable learning assistants for educational content sites

= ⚡ Compatibility =

* WordPress 5.5+
* Works with any theme
* Supports custom post types
* Fully compatible with NLWeb protocol (v0.1)

= 📈 Roadmap =

* LangGraph-powered backend runtime (optional)
* LangSmith-style observability and logs
* Webhook triggers to auto-ingest updates into NLWeb
* Fine-tuning prompt behavior per site or post type

== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Visit the plugin settings to:
   * Enable/disable content types (posts, pages, custom types)
   * Set metadata fields (e.g. podcast feed, author name)
   * Paste in your NLWeb server endpoint
4. The chatbot will appear on your site, ready to answer questions about your content

== Frequently Asked Questions ==

= Do I need an API key for this to work? =

Yes, you'll need an OpenAI API key, along with an NLWeb server. We have a free, open source NLWeb server implementation that's compatible with this plugin here:
You can one click deploy it on Railways, then copy/paste the URL into the plugin settings under settings -> NLWeb Chatbot Settings

= Will this work with my theme? =

Yes, the plugin is designed to work with any WordPress theme.

= Does this slow down my site? =

No. The chat widget is extremely lightweight, and all AI processing happens on the NLWeb server, not your WordPress site.

= Can I customize how the chatbot looks? =

Yes, you can either customize it through modifying the HTML code in the plugin.
If you want us to add settings to customize the style and feel, please create an issue in our repo.

= How does this differ from other AI chat plugins? =

1. Completely free and open source.
2. NLWeb for WordPress grounds all AI responses in your actual content rather than generating potentially inaccurate responses. It uses your site's content as the knowledge base.

== Changelog ==

= 1.0.0 =
* Initial release with NLWeb RSS feed and chat interface

== Upgrade Notice ==

= 1.0.0 =
Initial release of NLWeb for WordPress. Bring conversational AI to your site!

== Contribute ==

We welcome contributions! PRs for better schema metadata, Gutenberg blocks, or dashboard controls are encouraged.

This plugin is part of an effort to bring open, conversational AI interfaces to the web — using protocols, not lock-in.

Want to customize this for your own site? Open an issue or contact me.

Please leave us a star on our GitHub repo if you find this helpful, or open an issue as to where we could improve it!