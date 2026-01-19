document.addEventListener('DOMContentLoaded', () => {
    const formAddCommentOuter = document.querySelector('.slt-comments-form-outer');
    const token = Joomla.getOptions('csrf.token');
    const uid = getCookie('SLT_COOKIE_UID');
    if (formAddCommentOuter) {
        makeFormAddComment(formAddCommentOuter);
    }
    let formAddComment = document.querySelectorAll('form[name="sltCommentForm"]');

    const commentsContainer = document.querySelector('.slt-comments-list-inner'); // контейнер, где выводятся комментарии
    let commentsItems = null;
    if (commentsContainer) {
        commentsItems = commentsContainer.querySelectorAll('.slt-comment-item:not(.moderate)');
    }
    addLikes(commentsItems,uid); // Добавляем лайки
    addAnswerComment(commentsItems,token,uid); // Подписка на событие клика по кнопке ответа

    if (formAddComment.length > 0) {
        formAddComment.forEach(form => startValidation(form));
    }
    function startValidation(form) {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            const formErrorElement = form.querySelector('.slt-comments-form-message');

            const formData = new FormData(form);
            try {
                const response = await fetch('/index.php?option=com_ajax&plugin=SltCommentsSubmit&group=content&format=raw', {
                    method: 'POST',
                    body: formData
                });
                if (response.ok) {
                    const result = await response.json();

                    if (result.data?.success) {
                        // Сохранить комментарий как "на модерации"
                        formData.set('idComment', result.data.idComment);

                        form.reset();
                        formErrorElement.classList.remove('alert-danger', 'alert-warning', 'p-0');
                        formErrorElement.classList.add('alert-success');
                        formErrorElement.innerHTML = result.data.message;
                    } else {
                        formErrorElement.classList.remove('alert-success', 'alert-danger', 'p-0');
                        formErrorElement.classList.add('alert-warning');
                        formErrorElement.textContent = 'Ошибка при отправке формы';
                        if (result.data?.error) {
                            formErrorElement.textContent += '. ' + result.data.error;
                        }
                    }
                } else {
                    formErrorElement.classList.remove('alert-success', 'alert-warning', 'p-0');
                    formErrorElement.classList.add('alert-danger');
                    formErrorElement.innerHTML = 'Ошибка сервера';
                }
            } catch (error) {
                console.error('Ошибка:', error);
                formErrorElement.classList.remove('alert-success', 'alert-warning', 'p-0');
                formErrorElement.classList.add('alert-danger');
                formErrorElement.innerHTML = 'Произошла ошибка при отправке.';
            }
        });
    }
    function addLikes(commentsItems,uid = '') {
        if (!commentsItems) return;
            commentsItems.forEach(item => {
                const btnsLike = item.querySelectorAll('.slt-comment-item-btn-like');
                if (btnsLike) {
                    btnsLike.forEach(btnLike => {
                        btnLike.addEventListener('click', (e) => {
                            sendLike('slt_comments_likes',e.target.dataset.id,e.target.dataset.type,e.target,token);
                        });
                    });
                }
            })
    }
    async function sendLike(key,idComment,type,el,token) {
        const stored = localStorage.getItem(key);
        let likes = [];

        if (stored) {
            try {
                likes = JSON.parse(stored);
                if (!Array.isArray(likes)) {
                    likes = [];
                }
            } catch (e) {
                console.warn('Invalid localStorage data, resetting.');
                likes = [];
            }
        }
        // Проверим, есть ли уже такой idComment
        const existingIndex = likes.findIndex(item => item.idComment === idComment);

        if (existingIndex !== -1) {
            if (likes[existingIndex].type !== type) {
                //Меняем мнение, запрос делать
                const formData = new FormData();
                formData.set(token, '1');
                formData.set('idComment', idComment);
                formData.set('type', type);

                try {
                    const response = await fetch('/index.php?option=com_ajax&plugin=SltCommentsLike&group=content&format=raw', {
                        method: 'POST',
                        body: formData
                    });

                    if (response.ok) {
                        const result = await response.json();
                        if (result.success) {
                            console.log(result.data)
                            //slt-comment__reaction
                            const parent = el.closest('.slt-comment__reaction');
                            console.log(parent)
                            if (parent) {
                                const likeCount = parent.querySelector('.slt-comment__like-count');
                                if (likeCount) {
                                    let count = '';
                                    if (result.data.count.likes > 0) {
                                        count = result.data.count.likes;
                                    }
                                    likeCount.innerText = count;
                                }
                                const dislikeCount = parent.querySelector('.slt-comment__dislike-count');
                                if (dislikeCount) {
                                    let count = '';
                                    if (result.data.count.dislikes > 0) {
                                        count = result.data.count.dislikes;
                                    }
                                    dislikeCount.innerText = count;
                                }
                            }
                        } else {
                            console.error('Ошибка при отправке формы');
                        }
                    } else {
                        console.error('Ошибка сервера');
                    }
                } catch (error) {
                    console.error('Ошибка:', error);
                }
            }
            else {
                console.error('Вы уже ставили такой лайк');
            }
            // Обновляем существующую запись
            likes[existingIndex].type = type;
        } else {
            // Добавляем новую запись
            likes.push({ idComment, type });
        }
        localStorage.setItem(key, JSON.stringify(likes));
    }
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
    }
    function setCookie(name, value, days) {
        const d = new Date();
        d.setTime(d.getTime() + days * 24 * 60 * 60 * 1000);
        document.cookie = `${name}=${value};expires=${d.toUTCString()};path=/`;
    }
    function addAnswerComment(commentsItems,token,uid) {
        if (!commentsItems) return;
        const policyAgreementLabel = document.querySelector('.policyAgreementLabel') ? document.querySelector('.policyAgreementLabel').innerHTML : '';
            commentsItems.forEach(item => {
                const btnAnswerComment = item.querySelector('.btn-comment__answer');
                const AnswerCommentList = item.querySelector('.slt-comment__answers-list');
                if (btnAnswerComment) {
                    btnAnswerComment.addEventListener('click', (e) => {
                        const formData = new FormData();
                        formData.set('content_item_id', e.target.dataset.articleid);
                        formData.set('parent_id', e.target.dataset.parentid);
                        formData.set('uid', uid);
                        if (policyAgreementLabel) {
                            formData.set('show_policy', policyAgreementLabel);
                        }
                        const formAnswerCommentOuter = AnswerCommentList.querySelector('.slt-comment__answers-list-form');
                        formAnswerCommentOuter.innerHTML = getFormAddComment(formData,token,'Ответ на комментарий');
                        formAnswerCommentOuter.classList.add('mt-3');
                        let formAddComment = document.querySelectorAll('form[name="sltCommentForm"]');
                        if (formAddComment.length > 0) {
                            formAddComment.forEach(form => startValidation(form));
                        }
                    });
                }
            })
    }
    function makeFormAddComment(selector) {
        const formData = new FormData(selector.querySelector('form'));
        selector.innerHTML = getFormAddComment(formData,token);
    }
    function getFormAddComment(formData,token, title = 'Добавить комментарий', ) {
        const showPolicy = formData.get('show_policy') !== '' ? formData.get('show_policy') : false;
        const max = 1000;
        const random = Math.floor(Math.random() * max) + 1;
        return `
            <form name="sltCommentForm" class="form-validate card text-bg-light mb-3 p-3">
                <fieldset class="row m-0">
                    <div class="col-12 mb-3">
                        <h5 class="sltCommentForm-title">${title}</h5>
                        <div class="slt-comments-form-message alert p-0 m-0" role="alert"></div>
                    </div>
                            <div class="col-12 col-md-6 form-group mb-3">
                                <label for="name_author" class="form-label required">Ваше имя</label>
                                <input
                                    type="text"
                                    name="name_author"
                                    class="form-control required"
                                    maxlength="100"
                                    required
                                    aria-required="true"
                                    placeholder="Введите ваше имя" 
                                    />
                            </div>
                    <div class="col-12 col-md-6 form-group mb-3">
                        <label for="comment" class="form-label required">Комментарий</label>
                        <textarea
                            name="comment"
                            class="form-control required"
                            rows="2"
                            maxlength="100"
                            required
                            aria-required="true"
                            placeholder="Введите ваш комментарий..."></textarea>
                    </div>
                    ${showPolicy ? `
                    <div class="col-12 form-group mb-3">
                        <div class="form-check">
                            <input
                                type="checkbox"
                                name="policy_agreement"
                                class="form-check-input required"
                                id="policyAgreement-${random}"
                                required
                                aria-required="true"
                                />
                            <label class="form-check-label policyAgreementLabel" for="policyAgreement-${random}">${showPolicy}</label>
                        </div>
                    </div>
                    ` : ''}
                    <div class="d-grid gap-2">
                        <input type="hidden" name="content_item_id" value="${formData.get('content_item_id')}" />
                        <input type="hidden" name="parent_id" value="${formData.get('parent_id')}" />
                        <input type="hidden" name="date_creation" value="${new Date().toISOString().slice(0, 19).replace('T', ' ')}" />
                        <input type="hidden" name="uid" value="${formData.get('uid')}" />
                        <input type="hidden" name="${token}" value="1" />
                        <button type="submit" class="btn btn-primary btn-lg">Отправить комментарий</button>
                    </div>
                </fieldset>
            </form>
        `;
    }
});