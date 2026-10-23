const form = document.querySelector("#search-form");
form.addEventListener("submit", async (event) => {
  event.preventDefault();
  const rows = document.querySelector("#results");
  rows.replaceChildren();
  document.querySelector("#error").textContent = "";
  try {
    const response = await fetch("task05.php?" + new URLSearchParams(new FormData(form)));
    const data = await response.json();
    if (!response.ok) throw new Error(data.error || "The search failed.");
    data.products.forEach((product) => {
      const row = document.createElement("tr");
      [product.type, product.brand, product.price, product.number, product.stock].forEach((value) => {
        const cell = document.createElement("td");
        cell.textContent = value;
        row.append(cell);
      }
      );
      rows.append(row);
    }
    );
  }
  catch (error) {
    document.querySelector("#error").textContent = error.message;
  }
}
);
