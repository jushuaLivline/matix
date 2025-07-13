import retainer from '../search/retainer'
import lodash from 'lodash'

const pathname = window.location.pathname
const customerCode = $('#customer_code')
const customerName = $('#customer_name')
const slipNumber = $('#_slip-no')
const instructionDate = $('#instruction_date')
const deliveryNumber = $('#_delivery-no')
const plant = $('#_plant')
const acceptance = $('#_acceptance')
const supplierCode = $('#supplier_code')
const supplierName = $('#supplier_name')

const customerNameKey = [pathname, customerName.attr('id')].join(':')

const reloadWithRequiredFilterParams = () => {
  const inputs = [
    customerCode, slipNumber, instructionDate, deliveryNumber,
    plant, acceptance, supplierCode, supplierName
  ]
  const params = new URLSearchParams
  lodash.each(inputs, input => params.set(input.prop('name'), input.val()))
  location.href = `${location.pathname}?${params.toString()}`
}

retainer(customerCode, customerName, customerNameKey)

$('#cache-shipment-data').on('click', () => {
  const _token = $('meta[name="csrf-token"]').attr('content')
  const productNumber = $('#_product-number')
  const productName = $('#_product-name')
  const quantity = $('#_quantity')
  const remarks = $('#_remarks')

  $.ajax({
    url: '/shipment-inspections/temp-data',
    type: 'post',
    data: {
      _token,
      part_no: productNumber.val(),
      part_name: productName.val(),
      quantity: quantity.val(),
      remarks: remarks.val()
    },
    success: () => {
      $('#warningInputs').hide()
      $('#successInputs').fadeIn(1000, reloadWithRequiredFilterParams)
    },
    error: ({ responseJSON }) => {
      const { errors } = responseJSON
      // Remove existing error classes
      $('#_product-number, #_product-name, #_quantity, #customer_code, #_slip-no, #instruction_date, #_delivery-no, #_plant, #_acceptance').removeClass('input-error')

      // Check for product-related input errors
      if (errors.part_no) productNumber.addClass('input-error')
      // if (errors.part_name) productName.addClass('input-error')
      if (errors.quantity) quantity.addClass('input-error')

      // Check if required fields are empty and apply 'input-error' class
      if (!customerCode.val()) customerCode.addClass('input-error')
      if (!slipNumber.val()) slipNumber.addClass('input-error')
      if (!instructionDate.val()) instructionDate.addClass('input-error')
      if (!deliveryNumber.val()) deliveryNumber.addClass('input-error')
      // if (!plant.val()) plant.addClass('input-error')
      // if (!acceptance.val()) acceptance.addClass('input-error')

      $('#warningInputs').show()
    },
  })
})