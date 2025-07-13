$(function(){
  // Select all input elements with the class 'numberCharacter'
  const inputs = document.querySelectorAll('.numberCharacter');
  const totalDisplay = document.getElementById('total');

  // Function to calculate and update the total
  const updateTotal = () => {
      let total = 0;

      // Loop through each input and sum the values
      inputs.forEach((input) => {
          const value = parseFloat(input.value) || 0; // Parse value or default to 0
          total += value;
      });

      // Update the total display with formatted number
      totalDisplay.textContent = total.toLocaleString(undefined, { maximumFractionDigits: 0 });
  };

  // Add event listeners to each input
  inputs.forEach((input) => {
      input.addEventListener('keyup', updateTotal); // Listen for keypress changes
  });

  // Initial call to update the total display
  updateTotal();


  const deleteRecordButton = document.querySelector('.deleteRecord');
  deleteRecordButton.addEventListener('click', (event)=> {
      const userConfirmed = confirm('当月の材料調達計画情報を削除します、よろしいでしょうか？');
      if (userConfirmed) {
          $('#deleteForm').submit();
      }
  })
});