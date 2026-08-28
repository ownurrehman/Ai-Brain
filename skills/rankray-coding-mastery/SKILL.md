---
name: rankray-coding-mastery
description: "Master playbook for RankRay full-stack software development (Next.js, Node/Express, TypeScript, Prisma, PostgreSQL), custom WordPress themes/plugins, and Hostinger API infrastructure."
---

# 💻 RankRay Full-Stack Coding & Infrastructure Mastery

> **The Single Source of Truth for Web Applications, API Development, WordPress Engineering, and Cloud Infrastructure.**

---

## 🚀 1. Modern Web & SaaS Engineering (Next.js / Node.js)

### 🧱 Tech Stack Guidelines
- **Frontend:** Next.js 14+ (App Router), React, TypeScript, Tailwind CSS.
- **Backend:** Node.js, Express, TypeScript, Prisma ORM, PostgreSQL.
- **State & Caching:** Redis, React Query / SWR.
- **Code Standards:** Strict type safety, clean controller/service/repository layers, explicit error handling.

---

## 🔌 2. Custom WordPress Theme & Plugin Engineering

### 🛠️ Core Patterns
- **ACF JSON Sync:** Store custom field definitions in `acf-json/` inside the theme for version-controlled schema syncing.
- **WP REST API Endpoints:** Build custom `/wp-json/rankray/v1/` routes authenticated via Application Passwords.
- **Speed Optimization:** Dequeue unused scripts, use webp images with explicit `width` and `height`, and implement transient caching for database queries.

---

## ☁️ 3. Hostinger Cloud & Server Infrastructure

- **API Integration:** Connect using Hostinger API (`https://developers.hostinger.com/api/hosting/v1/`) with Bearer auth.
- **PHP & Web Server:** Manage PHP 8.2+, LiteSpeed Cache, SSL certificates, and DNS records.
- **Safe Deployments:** Always verify local builds (`npm run build` or `composer check`) before syncing files to staging or production.
