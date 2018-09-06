import taglist from '@/components/admin/taglist'
import tagcreate from '@/components/admin/tagcreate'
import tagmodify from '@/components/admin/tagupdate'

export default [
  {
    path: 'tag',
    component: taglist
  },
  {
    path: 'tag/create',
    component: tagcreate
  },
  {
    path: 'tag/modify',
    name: 'tag/modify',
    component: tagmodify
  }

]
