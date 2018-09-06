import postlist from '@/components/admin/postlist'
import postcreate from '@/components/admin/postcreate'
import postmodify from '@/components/admin/postupdate'

export default [
  {
    path: 'post',
    component: postlist
  },
  {
    path: 'post/create',
    component: postcreate
  },
  {
    path: 'post/modify',
    name: 'post/modify',
    component: postmodify
  }

]
