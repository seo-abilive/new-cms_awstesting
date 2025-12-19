import { useMemo, useRef } from 'react'
import { useSearchParams } from 'react-router-dom'
import { useAxios } from '@/utils/hooks/useAxios'
import { TextInput } from '@/utils/components/ui/form/TextInput'
import { Label } from '@/utils/components/ui/form'
import { Button } from '@/utils/components/ui/button'
import { Spinner } from '@/utils/components/ui/spinner'
import { Alert } from '@/utils/components/ui/alert'
import { config as userConfig } from '../utils/config'
import { useNavigation } from '@/utils/hooks/useNavigation'
import { toast } from 'sonner'

export const ResetForm = () => {
    const { sendRequest, loading, error, validationErrors } = useAxios()
    const [params] = useSearchParams()
    const { navigateTo } = useNavigation()
    const ref = useRef({ password: '', password_confirmation: '' })

    const token = useMemo(() => params.get('token') || '', [params])
    const email = useMemo(() => params.get('email') || '', [params])

    const onSubmit = async () => {
        await sendRequest({
            method: 'GET',
            url: `${
                import.meta.env.VITE_API_ORIGIN || 'http://localhost:8000'
            }/sanctum/csrf-cookie`,
        })
        const res = await sendRequest({
            method: 'POST',
            url: `${userConfig.end_point}/password/reset`,
            data: {
                token,
                email,
                password: ref.current.password,
                password_confirmation: ref.current.password_confirmation,
            },
        })
        if (res?.success) {
            toast.success('パスワードを更新しました。ログインしてください。')
            navigateTo('/login')
        }
    }

    return (
        <div>
            <h2 className="mb-4 font-semibold text-gray-800 text-title-sm dark:text-white/90">
                パスワード再設定
            </h2>
            {error && (
                <Alert color="failure" className="mb-2">
                    {error.response?.data?.message || 'エラーが発生しました'}
                </Alert>
            )}
            <div className="space-y-6">
                <div>
                    <Label>新しいパスワード</Label>
                    <TextInput type="password" onChange={(v) => (ref.current.password = v)} />
                    {validationErrors?.password && (
                        <p className="text-error-500">{validationErrors.password[0]}</p>
                    )}
                </div>
                <div>
                    <Label>新しいパスワード（確認）</Label>
                    <TextInput
                        type="password"
                        onChange={(v) => (ref.current.password_confirmation = v)}
                    />
                </div>
                <Button onClick={onSubmit} className="w-full" disabled={loading}>
                    {loading ? <Spinner className="size-4" /> : '更新する'}
                </Button>
            </div>
        </div>
    )
}
