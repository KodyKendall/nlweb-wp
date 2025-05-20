# 🧠 NLWeb for WordPress

**Bring AI-powered chat to your WordPress site — using your own content.**

This plugin exposes your site content through a clean, structured RSS feed compatible with [Microsoft's NLWeb protocol](https://github.com/microsoft/nlweb), and adds a customizable AI chatbot interface directly on your site. Users can now **converse with your blog posts, podcast episodes, and pages** — not just search them.

---
## ✨ Features

- ✅ **Auto-generated NLWeb-compatible RSS feed** of your site's content
- 🧠 **AI chat interface** embedded via `<nlweb-chat>` powered by your content
- ⚙️ Easily **customize branding, prompt behavior, and chat appearance**
- 🚀 Works out of the box with open-source [NLWeb server](https://github.com/microsoft/nlweb)
- 💬 Users can **ask natural language questions** and get AI responses grounded in your real content
---

## 🖼️ Demo

https://yourdomain.com → “Ask me anything about our blog.”

> “What’s the difference between LangChain and LangGraph?”  
> → “According to our blog post from April 2024: LangGraph is a state machine execution framework for LLM agents...”

---

## 📦 Installation

1. Upload this plugin to your WordPress site (`/wp-content/plugins/nlweb-wp/`)
2. Activate the plugin from your WordPress admin dashboard
3. Visit the plugin settings to:
   - Enable/disable content types (posts, pages, custom types)
   - Set metadata fields (e.g. podcast feed, author name)
   - Paste in your NLWeb server endpoint
4. Paste the chatbot widget into any page:


🛠️ Under the Hood
RSS Feed: Available at /feed (default for all WordPress sites)

Structured using schema.org metadata

Includes title, URL, content excerpt, and published date

nlweb-chatbot.php: Injects basic chat component to your frontend

Custom Settings Panel: Configure feed behavior, server URL, styling

💡 Use Cases
📚 Let visitors ask your blog questions

🎙️ Let fans chat with podcast transcripts

📈 Turn static SEO content into dynamic interactive experience

👩‍🏫 Enable learning assistants for educational content sites

⚡ Compatibility
WordPress 5.5+

Works with any theme

Supports custom post types

Fully compatible with NLWeb protocol (v0.1)

📈 Roadmap
 LangGraph-powered backend runtime (optional)

 LangSmith-style observability and logs

 Webhook triggers to auto-ingest updates into NLWeb

 Fine-tuning prompt behavior per site or post type

🤝 Contribute
We welcome contributions! PRs for better schema metadata, Gutenberg blocks, or dashboard controls are encouraged.

🧑‍💻 Built by @kodykendall
This plugin is part of an effort to bring open, conversational AI interfaces to the web — using protocols, not lock-in.

Want to customize this for your own site? Open an issue or contact me.

📄 License
MIT. Use freely. Improve openly.