$(() => {
  $('#product_code').on('input', () => $('#product_name').val(''))

  $('.edit-material-number').on('input', event => {
    const id = $(event.target).attr('material')
    $(`#product_name_${id}`).val('')
  })

  const disableEditableColumns = (id, disabled = true) => {
    const inputs = [`#instruction_date_${id}`, `#flight_no_${id}`,
      `#delivery_no_${id}`, `#product_code_${id}`, `#arrival_quantity_${id}`]
    const names = [`#product_name_${id}`]
    const buttons = [`button[data-target=instruction_date_${id}]`,
      `button[data-target=searchProductModal_${id}]`]
    if (disabled) {
      [...inputs, ...names].forEach(selector => {
        const element = $(selector)
        const old = element.attr('old')
        element.val(old)
        element.text(old)
      })
    }
    [...inputs, ...buttons].forEach(selector => $(selector).prop('disabled', disabled))
  }

  $('.edit').on('click', event => {
    const id = $(event.target).attr('material')
    disableEditableColumns(id, false)
    $(`#EditDelete${id}`).hide()
    $(`#UdpateUndo${id}`).show()
  })

  $('.undo').on('click', event => {
    const id = $(event.target).attr('material')
    if (!confirm('キャンセルしますか？')) return
    disableEditableColumns(id)
    $(`#EditDelete${id}`).show()
    $(`#UdpateUndo${id}`).hide()
  })
})
