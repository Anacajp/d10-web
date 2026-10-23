document.querySelector("#product-form").addEventListener("submit", async (event) => {
  event.preventDefault();
  try {
    const response = await fetch("task06.php", {
      method: "POST", body: new FormData(event.target)
    }
    );
    const data = await response.json();
    if (!response.ok) throw new Error(data.error || "Could not save product.");
    document.querySelector("#message").textContent = data.message;
  }
  catch (error) {
    document.querySelector("#message").textContent = error.message;
  }
}
);
