import store from '~/store'

export default async (to, from, next) => {
  if (store.getters['operations/tenant'] && !store.getters['operations/tenant'].is_subscribed) {
    if (to.name === 'settings.billing') {
      next()
    }
    else{
      next({ name: 'settings.billing' })
    }
  }

  next()
};
