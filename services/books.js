export const getAllBooks = () => {
  return fetch("api/books.php").then((response) => response.json());
};
