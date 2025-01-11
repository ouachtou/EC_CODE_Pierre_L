function openBookModal(bookId, description, rating, isRead, id) {
    console.log(bookId, description, rating, isRead, id);
    // Set the values in the modal form
    document.querySelector('[name="book_read_form[id]"]').value = id;
    document.querySelector('[name="book_read_form[book_id]"]').value = bookId;
    document.querySelector('[name="book_read_form[description]"]').value = description;
    document.querySelector('[name="book_read_form[rating]"]').value = rating;
    document.querySelector('[name="book_read_form[is_read]"]').checked = isRead === 'checked';

    // Open the modal (if using a library, you might need to call a specific function)
    const modal = document.getElementById('book_modal');
    modal.style.display = 'block'; // Example to show the modal
}