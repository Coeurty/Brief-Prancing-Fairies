// TODO: refacto this file

// New topic category
const newTopicCategoryBtn = document.getElementById("newTopicCategoryBtn");
const newTopicCategoryForm = document.getElementById("newTopicCategoryForm");
const cancelNewTopicCategoryBtn = document.getElementById(
  "cancelNewTopicCategoryBtn"
);

if (newTopicCategoryBtn && newTopicCategoryForm && cancelNewTopicCategoryBtn) {
  newTopicCategoryBtn.addEventListener("click", showNewTopicCategoryForm);
  cancelNewTopicCategoryBtn.addEventListener("click", hideNewTopicCategoryForm);

  function showNewTopicCategoryForm() {
    newTopicCategoryForm.style.display = "block";
    newTopicCategoryBtn.style.display = "none";
  }

  function hideNewTopicCategoryForm() {
    newTopicCategoryForm.style.display = "none";
    newTopicCategoryBtn.style.display = "block";
  }
}

// Edit topic category
const editTopicCategoryBtns = document.querySelectorAll(
  ".editTopicCategoryBtn"
);
const editTopicCategoryForms = document.querySelectorAll(
  ".editTopicCategoryForm"
);
const cancelEditTopicCategoryBtns = document.querySelectorAll(
  ".cancelEditTopicCategoryBtn"
);

editTopicCategoryBtns.forEach((btn) => {
  btn.addEventListener("click", () => {
    btn.style.display = "none";
    topicCategoryId = btn.dataset.topiccategoryid;
    editTopicCategoryForms.forEach((form) => {
      form.style.display = "none";
      if (form.dataset.topiccategoryid == topicCategoryId) {
        form.style.display = "block";
      }
    });
  });
});

cancelEditTopicCategoryBtns.forEach((btn) => {
  btn.addEventListener("click", () => {
    editTopicCategoryBtns.forEach((btn) => {
      btn.style.display = "block";
    });

    editTopicCategoryForms.forEach((form) => {
      form.style.display = "none";
    });
  });
});
