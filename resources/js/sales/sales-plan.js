$(() => {
  const overlay = $('.submit-overlay')
  $('#export-excel').on('click', () => {
    const _token = $('meta[name="csrf-token"]').attr('content')
    const serialized = $('#sale_plan_form').serializeArray()
    const filtered = serialized.filter(input => input.value.trim())
    const data = filtered.reduce((previous, current) => {
      previous[current.name] = current.value
      return previous
    }, { _token })
    overlay.css('display', 'flex')
    $.ajax({
      url: '/sales/export',
      type: 'post',
      data,
      xhrFields: {
        responseType: 'blob',
      },
      success: response => {
        const anchor = document.createElement('a')
        anchor.href = window.URL.createObjectURL(response)
        anchor.download = '発注金額明細表.xlsx'
        anchor.click()
        window.URL.revokeObjectURL(anchor.href)
        overlay.hide()
      },
      error: error => console.log(error),
    })
  })
})
