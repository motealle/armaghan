import { afterEach, expect, it, vi } from 'vitest'
import { createImageRecovery } from './imageRecovery'
afterEach(()=>vi.useRealTimers())
it('recovers a transient photo error with two delayed fresh requests, then stops',()=>{
  vi.useFakeTimers();const retry=vi.fn(),recovery=createImageRecovery(retry)
  const source='/backend/api/catalog/media/42/thumb?v=release'
  expect(recovery.failed(source)).toBe(true)
  expect(recovery.failed(source)).toBe(true)
  expect(retry).not.toHaveBeenCalled()
  vi.advanceTimersByTime(1500)
  expect(retry).toHaveBeenCalledOnce()
  expect(retry.mock.calls[0]![0]).toMatch(/thumb\?v=release&image_retry=.+-1$/)
  expect(recovery.failed(source)).toBe(true)
  vi.advanceTimersByTime(4000)
  expect(retry).toHaveBeenCalledTimes(2)
  expect(recovery.failed(source)).toBe(false)
})
it('cancels old-product retries on navigation and does not retry missing placeholders',()=>{
  vi.useFakeTimers();const retry=vi.fn(),recovery=createImageRecovery(retry)
  expect(recovery.failed('/images/default.webp')).toBe(false)
  recovery.failed('/backend/api/catalog/media/42/thumb');recovery.reset()
  vi.runAllTimers();expect(retry).not.toHaveBeenCalled()
  recovery.failed('/backend/api/catalog/media/43/thumb');recovery.cancel()
  vi.runAllTimers();expect(retry).not.toHaveBeenCalled()
})

it('manual retry starts immediately with a fresh URL and renews the bounded automatic attempts',()=>{
  vi.useFakeTimers();const retry=vi.fn(),recovery=createImageRecovery(retry)
  const src='/backend/api/catalog/media/42/thumb?v=release'
  recovery.failed(src);vi.advanceTimersByTime(1500)
  recovery.failed(src);vi.advanceTimersByTime(4000)
  expect(recovery.failed(src)).toBe(false)
  recovery.retryNow(src)
  expect(retry).toHaveBeenCalledTimes(3)
  expect(retry.mock.calls[2]![0]).toMatch(/&image_retry=.+-0$/)
  expect(recovery.failed(src)).toBe(true)
  vi.advanceTimersByTime(1500)
  expect(retry).toHaveBeenCalledTimes(4)
})
