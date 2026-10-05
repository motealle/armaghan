// One observer for all product images. Percent CSS margins use viewport width,
// so calculate pixels from height to implement the owner's 30% screen preload.
const pending = new Map<Element, () => void>()
let observer: IntersectionObserver | undefined
let listening = false

function rebuild() {
  observer?.disconnect()
  const margin = Math.round(window.innerHeight * 0.3)
  observer = new IntersectionObserver(entries => {
    for (const entry of entries) {
      if (!entry.isIntersecting) continue
      const activate = pending.get(entry.target)
      pending.delete(entry.target)
      observer?.unobserve(entry.target)
      activate?.()
    }
    if (!pending.size) stop()
  }, { rootMargin: `${margin}px 0px ${margin}px 0px` })
  for (const element of pending.keys()) observer.observe(element)
}

function stop() {
  observer?.disconnect()
  observer = undefined
  window.removeEventListener('resize', rebuild)
  listening = false
}

export function whenNearViewport(element: Element, activate: () => void) {
  if (!('IntersectionObserver' in window)) {
    activate()
    return () => {}
  }
  pending.set(element, activate)
  if (!listening) {
    listening = true
    window.addEventListener('resize', rebuild, { passive: true })
    rebuild()
  } else observer?.observe(element)
  return () => {
    pending.delete(element)
    observer?.unobserve(element)
    if (!pending.size) stop()
  }
}
