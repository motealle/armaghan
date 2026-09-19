# Test 12 — Vue 3 Product Foundation

## Goal
Replace the prototype-style Test 11 implementation with a maintainable, testable Vue 3 foundation while preserving the strongest UX decisions from Test 10.

## Source
`/platform/frontend`

## Generated validation build
`/t/12` during CI.

## Stack
- Vue 3 Single-File Components with `<script setup>`
- TypeScript strict mode
- Vite
- Vue Router
- Pinia
- Tailwind CSS Vite plugin
- Lucide Vue
- Vitest

## Required flows
- guest catalog
- three product WhatsApp paths
- three production wizard paths
- product details with locked/negotiable specs
- favorites
- admin/customer demo login and global logout
- customer timeline
- admin customer list
- impersonation + stop
- demo product CRUD
- demo image upload/compression
- hidden Design Lab

## Production transition
Test 12 uses browser mock persistence only to validate behavior. Real state moves to Laravel 13 with SQLite first and MySQL later. Filament 5 is the permanent admin control plane; the Vue admin screen is an interaction-validation slice, not a duplicate permanent admin framework.

## Assets
See `docs/ASSET-MANAGEMENT.md`.
