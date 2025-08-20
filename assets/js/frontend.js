console.log('initializing app.js')

document.addEventListener('DOMContentLoaded', () => {
  // -- popups

  const body = document.querySelector('body')
  const popups = document.querySelectorAll('.popup')
  const openPopups = document.querySelectorAll('.button-popup')
  const closePopups = document.querySelectorAll(
    '.popup__btn-close, .popup__overlay'
  )
  const popupContacts = document.getElementById('popup-contacts')
  const popupVideo = document.querySelector('.popup__video')

  openPopups.forEach((button) => {
    button.addEventListener('click', function (e) {
      e.preventDefault()
      const popupId = this.getAttribute('data-popup')

      popups.forEach((popup) => {
        if (popup.id === popupId) {
          popup.classList.add('open')

          if (popup.id === 'popup-video') {
            setTimeout(() => {
              popupVideo.play()
            }, 1000)
          }
        } else {
          popup.classList.remove('open')
        }
        body.classList.add('lock')
      })
    })
  })

  closePopups.forEach((button) => {
    button.addEventListener('click', function () {
      const popup = this.closest('.popup')
      popup.classList.remove('open')
      body.classList.remove('lock')

      if (popup.id === 'popup-video') {
        popupVideo.pause()
        setTimeout(() => {
          popupVideo.currentTime = 0
        }, 1000)
      }
    })
  })

  function lockBodyScroll(action) {
    if (action == 'lock') body.classList.toggle('lock')
    else if (action == 'unlock') body.classList.remove('lock')
  }

  // -- steps
  let stepsElements = document.querySelectorAll('.gifts-step')
  let activeStep = 1

  function updateStep(direction) {
    if (direction === '+') {
      if (activeStep < 3) {
        activeStep++
        changeStep(activeStep)
      }
    } else if (direction === '-') {
      if (activeStep > 1) {
        activeStep--
        changeStep(activeStep)
      }
    }
  }

  function changeStep(step) {
    stepsElements.forEach((el) => {
      el.classList.remove('active')
    })

    let activeElem = stepsElements[step - 1]
    activeElem.classList.add('active')

    const y = activeElem.getBoundingClientRect().top + window.scrollY - 100
    window.scrollTo({
      top: y,
      behavior: 'smooth',
    })
  }

  // // !temp
  document.addEventListener('keydown', (e) => {
    if (e.key === '+' || e.key === '=') {
      updateStep('+')
    }
    if (e.key === '-') {
      updateStep('-')
    }
  })

  // --- Allow only digits and "+" at the start for all tel inputs ---
  document.querySelectorAll('input[type="tel"]').forEach((input) => {
    input.addEventListener('input', () => {
      let value = input.value

      // "+" only at the beginning
      if (value.startsWith('+')) {
        // leave "+" and allow only up to 15 digits
        value =
          '+' +
          value
            .slice(1)
            .replace(/[^0-9]/g, '')
            .slice(0, 15)
      } else {
        // only digits, max 15
        value = value.replace(/[^0-9]/g, '').slice(0, 15)
      }

      input.value = value
    })
  })

  // --- Form validation ---

  function validateForm(form) {
    let isValid = true
    const messages = form.querySelector('.messages')
    if (messages) messages.textContent = ''

    const phoneInput = form.querySelector('input[type="tel"]')
    const digits = phoneInput.value.replace(/\D/g, '')

    if (digits.length < 10 || digits.length > 15) {
      if (messages) {
        messages.textContent = 'מספר טלפון שגוי הוזן.'
        phoneInput.classList.add('error')
      } else {
        messages.textContent = ''
        phoneInput.classList.remove('error')
      }
      isValid = false
    }

    return isValid
  }

  // -- step 1: download file

  document.getElementById('form-contacts').addEventListener('submit', (e) => {
    e.preventDefault()

    // validate form
    const form = e.target
    if (!validateForm(form)) {
      return // stop if error
    }

    // download file
    const a = document.createElement('a')
    a.href = '/wp-content/plugins/excel-to-lionwheel/assets/files/test.xlsx'
    a.download = ''
    document.body.appendChild(a)
    a.click()
    a.remove()

    // close popup
    popupContacts.classList.remove('open')
    lockBodyScroll('unlock')

    // next step
    setTimeout(() => {
      updateStep('+')
    }, 700)
  })

  // -- step 2: check is file downloaded

  document.addEventListener('change', function (e) {
    if (e.target.matches('.step-2 input[type="file"]')) {
      const labelWrap = e.target.closest('label')
      if (e.target.files.length > 0) {
        labelWrap.classList.add('active')

        setTimeout(() => {
          updateStep('+')
        }, 700)
      } else {
        labelWrap.classList.remove('active')
      }
    }
  })

  // -- step 3: send form

  // old code

  // document
  //   .getElementById('form-order')
  //   .addEventListener('submit', async (e) => {
  //     e.preventDefault()

  //     // validate form
  //     const form = e.target
  //     if (!validateForm(form)) {
  //       return // stop if error
  //     }

  //     // collect form data
  //     const formData = new FormData(form)
  //     const fileInput = document.querySelector('.step-2 input[type="file"]')
  //     if (fileInput.files.length > 0) {
  //       formData.append('excel_file', fileInput.files[0])
  //     }

  //     // log form data for debugging
  //     console.log('Form data:', Object.fromEntries(formData.entries()))

  //     // try {
  //     //   // send form data to WordPress endpoint
  //     //   const response = await fetch(
  //     //     wpApiSettings.root + 'wp/v2/your-endpoint',
  //     //     {
  //     //       method: 'POST',
  //     //       headers: {
  //     //         'X-WP-Nonce': wpApiSettings.nonce, // WordPress nonce for authentication
  //     //       },
  //     //       body: formData,
  //     //     }
  //     //   )

  //     //   if (!response.ok) {
  //     //     throw new Error('Network response was not ok')
  //     //   }

  //     //   // show success message
  //     //   alert('ההזמנה נשלחה בהצלחה!')

  //     //   // reset form
  //     //   form.reset()
  //     // } catch (error) {
  //     //   console.error('Error submitting form:', error)
  //     //   alert('Помилка при відправленні форми. Спробуйте ще раз.')
  //     // }
  //   })

  // end
})

// Frontend JavaScript for Excel to Lionwheel plugin
jQuery(document).ready(function ($) {
  // Form validation
  $('#form-order').on('submit', function (e) {
    var name = $('#name').val().trim()
    var phone = $('#phone').val().trim()
    var email = $('#email').val().trim()
    // var fileInput = $('#excel_file')

    // Basic validation
    if (!name) {
      alert('Please enter your name.')
      e.preventDefault()
      return false
    }

    if (!phone) {
      alert('Please enter your phone number.')
      e.preventDefault()
      return false
    }

    if (!email) {
      alert('Please enter your email address.')
      e.preventDefault()
      return false
    }

    // Email validation
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    if (!emailRegex.test(email)) {
      alert('Please enter a valid email address.')
      e.preventDefault()
      return false
    }

    // Show loading indicator
    var submitBtn = $(this).find('input[type="submit"]')
    submitBtn.prop('disabled', true).val('Processing...')
  })
})

// // File validation if file is selected
// if (fileInput[0].files.length > 0) {
//   var fileName = fileInput.val()
//   var ext = fileName.split('.').pop().toLowerCase()
//   if ($.inArray(ext, ['xlsx', 'xls']) === -1) {
//     alert('Please upload only Excel files (.xlsx or .xls).')
//     e.preventDefault()
//     return false
//   }

//   // File size check (max 10MB)
//   var fileSize = fileInput[0].files[0].size
//   var maxSize = 10 * 1024 * 1024 // 10MB
//   if (fileSize > maxSize) {
//     alert('File is too large. Maximum size is 10MB.')
//     e.preventDefault()
//     return false
//   }
// }
