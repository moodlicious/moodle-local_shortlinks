import notification from "core/notification";
import {getString} from "core/str";
import {add as addToast} from "core/toast";
import ModalForm from "core_form/modalform";

export const init = () => {
    /** @type HTMLAnchorElement|null */
    const button = document.querySelector("#create_shortlink");
    if (!button) {
        return;
    }

    button.addEventListener("click", (e) => {
        e.preventDefault();
        const target = e.target;

        const modal = new ModalForm({
            formClass: "local_shortlinks\\form\\create_shortlink",
            returnFocus: target,
            modalConfig: {title: getString("form:create", "local_shortlinks")},
            saveButtonText: getString("create"),
        });

        modal.addEventListener(modal.events.FORM_SUBMITTED, ({detail}) =>
            detail.success
                ? getString("success:created", "local_shortlinks")
                      .then((message) => addToast(message, {}))
                      .then(() => window.location.reload())
                      .catch(notification.exception)
                : getString("error:create", "local_shortlinks")
                      .then((message) => addToast(message, {}))
                      .catch(notification.exception),
        );

        modal.show();
    });
};
