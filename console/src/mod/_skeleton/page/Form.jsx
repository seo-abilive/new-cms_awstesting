import { ResourceForm } from '@/utils/components/common/ResourceForm'
import { config } from '../utils/config'
import { useParams } from 'react-router'

const Form = ({ pageName }) => {
    const { id } = useParams()
    const breads = [{ name: config.name, path: config.path }, { name: pageName }]
    const formItem = [{ title: 'タイトル', id: 'title', required: true }]

    return (
        <>
            <ResourceForm options={{ breads, config, formItem, id }} />
        </>
    )
}

export const New = () => {
    return (
        <>
            <Form pageName={'新規作成'} />
        </>
    )
}

export const Edit = () => {
    return (
        <>
            <Form pageName={'編集'} />
        </>
    )
}
