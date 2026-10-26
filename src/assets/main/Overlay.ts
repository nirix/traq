/*!
 * Traq
 * Copyright (C) 2009-2026 Jack Polgar
 * Copyright (C) 2012-2026 Traq.io
 * https://github.com/nirix
 * http://traq.io
 *
 * This file is part of Traq.
 *
 * Traq is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 only.
 *
 * Traq is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Traq. If not, see <http://www.gnu.org/licenses/>.
 */

import Alpine from 'alpinejs'

declare global {
  interface Window {
    close_overlay: () => void
  }
}

// [data-overlay] on the initial page and on HTML added or changed later.
// Capture so this runs before the link navigates and before ancestor handlers.
document.addEventListener(
  'click',
  (event) => {
    const target = event.target

    if (!(target instanceof Element)) {
      return
    }

    const element = target.closest('[data-overlay]')
    if (!element) {
      return
    }

    event.preventDefault()
    event.stopPropagation()

    openOverlay(element)
  },
  true,
)

function openOverlay(element: Element) {
  // Create the modal container
  const modalContainer = document.createElement('div')
  modalContainer.id = 'modal-container'
  modalContainer.classList.add('modal-container')

  // Create the "modal" but really just the container for the loaded in modal
  const modal = document.createElement('div')
  modal.id = 'modal'
  modal.classList.add('modal')
  modalContainer.appendChild(modal)

  // Build path with query params
  const attrValue = element.getAttribute('data-overlay')
  const path = attrValue === '1' ? element.getAttribute('href') : element.getAttribute('data-overlay')
  const pathBits = path?.split('?')
  const searchParams = new URLSearchParams(pathBits?.[1] ?? '')
  searchParams.set('overlay', 'true')
  const fetchPath = pathBits?.[0] + '?' + searchParams.toString()

  // On click-outside of modal, close the modal
  modalContainer.addEventListener('click', (event) => {
    if (event.target === modalContainer) {
      window.close_overlay()
    }
  })

  // On escape key, close the modal
  window.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      window.close_overlay()
    }
  })

  fetch(fetchPath, {
    method: 'GET',
  })
    .then((response) => response.text())
    .then((html) => {
      modal.innerHTML = html
      document.body.appendChild(modalContainer)

      // Initialize Alpine
      Alpine.initTree(modal)
    })
    .catch((error) => {
      console.error(error)
    })
    .finally(() => {
      // Wait a couple of ms to make sure the modal is loaded
      setTimeout(() => {
        modalContainer.classList.add('open')
      }, 10)
    })
}

window.close_overlay = () => {
  const modalContainer = document.getElementById('modal-container')
  if (modalContainer) {
    modalContainer.classList.remove('open')

    // Wait for the transition to complete
    setTimeout(() => {
      if (modalContainer) {
        document.body.removeChild(modalContainer)
      }
    }, 300)
  }
}
