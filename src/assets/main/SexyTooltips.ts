/*!
 * Sexy tooltips
 * Copyright (c) 2012-2026 Jack P.
 * All Rights Reserved
 * https://github.com/nirix
 *
 * Released under the BSD 3-clause license.
 */

type TooltipPosition = 'right' | 'top'

const TOOLTIP_ID = 'sexytooltip'
const FADE_MS = 200

let tooltipEl: HTMLDivElement | null = null
let fadeTimer: number | undefined
let generation = 0
const leaveHandlers = new WeakMap<HTMLElement, () => void>()

function px(value: string): number {
  const parsed = parseFloat(value)
  return Number.isFinite(parsed) ? parsed : 0
}

function padding(el: HTMLElement, side: 'top' | 'right' | 'bottom' | 'left'): number {
  return px(getComputedStyle(el).getPropertyValue(`padding-${side}`))
}

function contentSize(el: HTMLElement): { width: number; height: number } {
  const style = getComputedStyle(el)
  const rect = el.getBoundingClientRect()

  return {
    width: rect.width - padding(el, 'left') - padding(el, 'right') - px(style.borderLeftWidth) - px(style.borderRightWidth),
    height: rect.height - padding(el, 'top') - padding(el, 'bottom') - px(style.borderTopWidth) - px(style.borderBottomWidth),
  }
}

function ensureTooltip(): HTMLDivElement {
  if (tooltipEl?.isConnected) {
    return tooltipEl
  }

  const existing = document.getElementById(TOOLTIP_ID)
  if (existing instanceof HTMLDivElement) {
    tooltipEl = existing
    return existing
  }

  const tip = document.createElement('div')
  tip.id = TOOLTIP_ID
  document.body.append(tip)
  tooltipEl = tip
  return tip
}

function cancelFade(tip: HTMLElement) {
  if (fadeTimer !== undefined) {
    window.clearTimeout(fadeTimer)
    fadeTimer = undefined
  }

  tip.style.transition = 'none'
}

function fadeIn(tip: HTMLElement) {
  cancelFade(tip)
  tip.style.display = 'block'
  tip.style.opacity = '0'
  void tip.offsetWidth
  tip.style.transition = `opacity ${FADE_MS}ms linear`
  tip.style.opacity = '1'
}

function fadeOut(tip: HTMLElement, shownAt: number) {
  cancelFade(tip)
  tip.style.transition = `opacity ${FADE_MS}ms linear`
  tip.style.opacity = '0'

  fadeTimer = window.setTimeout(() => {
    if (shownAt !== generation) {
      return
    }

    tip.style.display = 'none'
    fadeTimer = undefined
  }, FADE_MS)
}

function measureTooltipHeight(tip: HTMLElement): number {
  const display = tip.style.display
  const visibility = tip.style.visibility
  tip.style.visibility = 'hidden'
  tip.style.display = 'block'
  const { height } = contentSize(tip)
  tip.style.display = display
  tip.style.visibility = visibility
  return height
}

function showTooltip(target: HTMLElement, position: TooltipPosition) {
  const tip = ensureTooltip()
  const shownAt = ++generation

  cancelFade(tip)
  tip.style.display = 'none'
  tip.classList.remove('sexytooltip-right', 'sexytooltip-top')

  const title = target.getAttribute('title')
  if (title) {
    target.setAttribute('data-tooltip', title)
    target.removeAttribute('title')
  }

  const text = target.getAttribute('data-tooltip')
  if (!text) {
    return
  }

  tip.innerHTML = text

  const targetSize = contentSize(target)
  const tipHeight = measureTooltipHeight(tip)
  const rect = target.getBoundingClientRect()
  const offsetLeft = rect.left + window.scrollX
  const offsetTop = rect.top + window.scrollY

  if (position === 'right') {
    tip.classList.add('sexytooltip-right')
    tip.style.left = `${offsetLeft + targetSize.width + padding(target, 'left') + padding(target, 'right') + padding(tip, 'left') + padding(tip, 'right')}px`
    tip.style.top = `${offsetTop - tipHeight / 2 + targetSize.height / 2 - 2}px`
  } else {
    tip.classList.add('sexytooltip-top')
    tip.style.left = `${offsetLeft}px`
    tip.style.top = `${offsetTop - targetSize.height - padding(target, 'top') - padding(target, 'bottom') - padding(tip, 'top') - padding(tip, 'bottom') - 5}px`
  }

  fadeIn(tip)

  const previousLeave = leaveHandlers.get(target)
  if (previousLeave) {
    target.removeEventListener('mouseleave', previousLeave)
  }

  const onLeave = () => {
    target.removeEventListener('mouseleave', onLeave)
    leaveHandlers.delete(target)
    fadeOut(tip, shownAt)
  }

  leaveHandlers.set(target, onLeave)
  target.addEventListener('mouseleave', onLeave)
}

function tooltipTarget(node: EventTarget | null): { el: HTMLElement; position: TooltipPosition } | null {
  if (!(node instanceof Element)) {
    return null
  }

  // Abbreviations in forms sit to the right. Everything else with a title sits above.
  const abbr = node.closest('form abbr')
  if (abbr instanceof HTMLElement) {
    return { el: abbr, position: 'right' }
  }

  const tipped = node.closest('[title], [data-tooltip]')
  if (!(tipped instanceof HTMLElement)) {
    return null
  }

  return { el: tipped, position: 'top' }
}

document.addEventListener('mouseover', (event) => {
  const match = tooltipTarget(event.target)
  if (!match) {
    return
  }

  const from = event.relatedTarget
  if (from instanceof Node && match.el.contains(from)) {
    return
  }

  showTooltip(match.el, match.position)
})
