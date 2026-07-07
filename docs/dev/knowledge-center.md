# Knowledge Center + Help Framework (P7 + P11)

- **Content**: `knowledge_articles` — platform-level (no merchant_id),
  Markdown body (rendered with HTML stripped + unsafe links blocked),
  category (faq / manual / getting_started / general), `video_url`
  placeholder, versioned per (slug, locale) — highest published version
  wins. Locale reads fall back to English per article.
- **Reading**: `/help` (categories + LIKE search), `/help/{slug}`.
  `KnowledgeService` is the only read path — swap search for something
  smarter later without touching controllers.
- **Context help (P11)**: articles carry a `context_key` (screen id, e.g.
  `members.index`). `<x-ui.help-button topic="members.index" />` renders the
  round "?" (tooltip + aria-label; `data-help-topic` hook for future
  walkthroughs) linking `/help/context/{key}` → article or Help Center.
  A global Help entry lives in the merchant topbar.
- **Authoring**: no admin UI yet — seeders/imports
  (`KnowledgeArticleSeeder` is the reference). Writing real content is
  content work, deliberately out of scope for the framework sprint.
