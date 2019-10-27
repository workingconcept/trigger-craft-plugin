'use strict';

/*
 global window,
 Craft
 */

const triggerNowBtn = document.querySelector('.trigger-option.trigger-now .heading');

triggerNowBtn.addEventListener('click', event => {
  Craft.postActionRequest('trigger/default/go', {}, function(data) {
    if (data.success) {
      Craft.cp.displayNotice(Craft.t('trigger', 'Build triggered.'))
    } else {
      Craft.cp.displayError(Craft.t('trigger', 'Failed to trigger a build.'))
    }
  });
});
