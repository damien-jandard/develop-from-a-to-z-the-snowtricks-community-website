const addFormToCollection = (e) => {
    const collectionHolder = document.querySelector('.' + e.currentTarget.dataset.collectionHolderClass);
  
    const item = document.createElement('div');
  
    item.innerHTML = collectionHolder
      .dataset
      .prototype
      .replace(
        /__name__/g,
        collectionHolder.dataset.index
      );
    
    item.querySelector(".btn-remove").addEventListener("click", () => item.remove());
  
    collectionHolder.appendChild(item);
  
    collectionHolder.dataset.index++;
};

document.querySelectorAll('.btn-new').forEach(btn => { btn.addEventListener("click", addFormToCollection)});

document.querySelectorAll('.btn-remove').forEach(btn => btn.addEventListener("click", (e) => e.currentTarget.closest(".row").remove()));