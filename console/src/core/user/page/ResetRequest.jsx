import { useRef } from 'react'
import { useAxios } from '@/utils/hooks/useAxios'
import { TextInput } from '@/utils/components/ui/form/TextInput'
import { Label } from '@/utils/components/ui/form'
import { Button } from '@/utils/components/ui/button'
import { Spinner } from '@/utils/components/ui/spinner'
import { Alert } from '@/utils/components/ui/alert'
import { config as userConfig } from '../utils/config'
import { toast } from 'sonner'
import { Link } from 'react-router'

export const ResetRequest = () => {
    const { sendRequest, loading, error, validationErrors } = useAxios()
    const ref = useRef({ email: '' })

    const onSubmit = async () => {
        // CSRF Cookie 取得
        await sendRequest({
            method: 'GET',
            url: `${
                import.meta.env.VITE_API_ORIGIN || 'http://localhost:8000'
            }/sanctum/csrf-cookie`,
        })
        const res = await sendRequest({
            method: 'POST',
            url: `${userConfig.end_point}/password/forgot`,
            data: { email: ref.current.email },
        })
        if (res?.success) {
            toast.success('パスワード再設定用のメールを送信しました。')
        }
    }

    return (
        <div>
            <h2 className="mb-4 font-semibold text-gray-800 text-title-sm dark:text-white/90">
                パスワード再設定メールの送信
            </h2>
            {error && (
                <Alert color="failure" className="mb-2">
                    {error.response?.data?.message || 'エラーが発生しました'}
                </Alert>
            )}
            <div className="space-y-6">
                <div>
                    <Label>メールアドレス</Label>
                    <TextInput type="email" onChange={(v) => (ref.current.email = v)} />
                    {validationErrors?.email && (
                        <p className="text-error-500">{validationErrors.email[0]}</p>
                    )}
                </div>
                <Button onClick={onSubmit} className="w-full" disabled={loading}>
                    {loading ? <Spinner className="size-4" /> : '送信する'}
                </Button>
                <div className="flex justify-end">
                    <Link
                        to="/login"
                        className="text-sm text-brand-500 hover:text-brand-600 dark:text-brand-400"
                    >
                        ログインへ戻る
                    </Link>
                </div>
            </div>
        </div>
    )
}
