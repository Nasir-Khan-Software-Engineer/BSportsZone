<?php

namespace App\Http\Controllers\Setup;
use App\Services\AccountSetup\IAccountSetupService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Exception;
use App\Models\Accountinfo;
use App\Models\POSSettings;
use Illuminate\Support\Facades\Auth;
use App\Models\LoyaltySetting;
use App\Models\SmsTemplate;
use App\Models\SmsConfig;
use App\Services\Sms\SmsTemplateBuilder;
class AccountSetupController extends Controller
{
    public function __construct(IAccountSetupService $accountSetupService)
    {
        // $this->middleware('auth');
        $this->accountSetupService = $accountSetupService;
    }

    public function index()
    {
        $POSID = $posid = auth()->user()->posid;
        $accountInfo = $this->accountSetupService->getAccountInfo($POSID);
        
        // Load SMS template
        $smsTemplate = SmsTemplate::where('posid', $posid)->first();
        
        // Default template if none exists
        $defaultTemplate = "Thanks for visiting [COMPANY_NAME].\n[## Inv:10235 Payable:Tk10000 Qty:5 Disc:Tk100 Adj:Tk10 ##]\nSee you again!";
        
        // Load SMS config
        $smsConfig = SmsConfig::where('posid', $posid)->first();
        
        return view('setup/accountSetup/index', [
            'accountInfo' => $accountInfo,
            'smsTemplate' => $smsTemplate ? $smsTemplate->template : $defaultTemplate,
            'smsConfig' => $smsConfig
        ]);
    }

    public function update(Request $request){
        try{
            
            $request->validate(
                [           
                    'companyName' => 'required|string|min:3|max:100',
                    //'logo' => 'required',
                    'primaryEmail' => 'required|email',
                    'secoundaryEmail' => 'nullable|email',
                    'primaryPhone' => 'required|digits:11',
                    'secondaryPhone' => 'nullable|digits:11',
                    'address' => 'required|string|min:3|max:500'
                ],
                [],
                [
                    'primaryPhone'   => 'Primary Phone Number',
                    'secondaryPhone' => 'Secondary Phone Number',
                    'address'        => 'Address',
                    //'logo'           => 'Logo',
                    'companyName'    => 'Company Name',
                    'primaryEmail'   => 'Primary Email',
                    'secoundaryEmail' => 'Secondary Email',
                ]
            );
    
            $accountInfo = [
                'POSID' => auth()->user()->posid,
                'companyName' => $request->companyName,
                'logo' => $request->input('logo'),
                'primaryEmail' => $request->primaryEmail,
                'secoundaryEmail' => $request->secoundaryEmail,
                'primaryPhone' => $request->primaryPhone,
                'secondaryPhone' => $request->secondaryPhone,
                'division' => "demodata",
                'district' => "demodata",
                'area' => "demodata",
                'address' => $request->address
            ];

            $posid = auth()->user()->posid;
            if ($request->has('logo')) {
                $base64Image = $request->input('logo');

                if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                    $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
                    $extension = strtolower($type[1]);

                    if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                        return response()->json(['error' => 'Invalid image type'], 422);
                    }
                } else {
                    return response()->json(['error' => 'Invalid base64 image format'], 422);
                }

                $base64Image = str_replace(' ', '+', $base64Image);
                $imageData = base64_decode($base64Image);

                if ($imageData === false) {
                    return response()->json(['error' => 'base64_decode failed'], 422);
                }

                $directory = public_path("images/{$posid}");

                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }

                $fileName = uniqid() . '.' . $extension;
                $filePath = $directory . '/' . $fileName;
                file_put_contents($filePath, $imageData);

                $accountInfo['logo'] = $fileName;
            } //  logo check end
    
            $accountInfo = $this->accountSetupService->updateAccountInfo($accountInfo);
            
            // js will refresh the page
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json(
            [
                'status'=>'success',
                'message'=>'Account info update successfully.',
                'accountInfo' => $accountInfo
            ]);
        }catch(ValidationException $exception){
            return response()->json(
                [
                    'status'=>'error', 
                    'message' => '', 
                    'errors' => $exception->validator->errors()
                ]
            );
        }catch(Exception $exception){
            return response()->json(
                [
                    'status'=>'error', 
                    'message' => 'Something went wrong, please try later.',                     
                ]
            );
        }
    }

    public function updatePosInformation(Request $request)
    {
        try {
            $validated = $request->validate(
                [
                    // Existing fields
                    'serviceCodePrefix' => 'required|string|min:3|max:5',
                    'invoiceNumberPrefix' => 'required|string|min:3|max:5',

                    // POS Adjustment fields (conditionally required)
                    'adjustment_min' => 'required|numeric',
                    'adjustment_max' => 'required|numeric|gte:adjustment_min',
                ],
                [], // Custom messages (optional)
                [
                    // Friendly attribute names
                    'serviceCodePrefix' => 'Service Code Prefix',
                    'invoiceNumberPrefix' => 'Invoice Number Prefix',
                    'adjustment_min' => 'Minimum Adjustment',
                    'adjustment_max' => 'Maximum Adjustment',
                ]
            );


            $posid = auth()->user()->posid;

            $accountInfo = Accountinfo::where('POSID', $posid)->first();
            $posSettings = POSSettings::where('posid', $posid)->first();

            if (!$accountInfo) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Account info not found.',
                ], 404);
            }

            if (!$posSettings) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'POS Information not found.',
                ], 404);
            }

            $accountInfo->productCodePrefix = $validated['serviceCodePrefix'];
            $accountInfo->invoiceNumberPrefix = $validated['invoiceNumberPrefix'];
            $accountInfo->updated_by = auth()->user()->id;
            $accountInfo->save();

            $posSettings->adjustment_min = $request->adjustment_min;
            $posSettings->adjustment_max = $request->adjustment_max;
            $posSettings->updated_by = auth()->user()->id; 
            $posSettings->save();

            // js will refresh the page
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json([
                'status' => 'success',
                'message' => 'POS Information updated successfully.',
            ]);
        }catch(ValidationException $exception){
            return response()->json(
                [
                    'status'=>'error', 
                    'message' => '', 
                    'errors' => $exception->validator->errors()
                ]
            );
        }  catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong, please try later.',
            ], 500);
        }
    }

    public function updateLoyaltySettings(Request $request)
    {
        try {

            if (!isFeatureEnabled('ENABLED_LOYALTY')) {
                return response()->json([
                    'status' => 'error',
                    'errors' => [
                        'feature' => ['Loyalty program feature is not enabled.']
                    ]
                ]);
            }

            // 1. Validate the request
            $validated = $request->validate([
                'minimum_sales_amount' => 'required|numeric|min:0',
                'validity_period_months' => 'required|integer|min:1',
                'max_visits' => 'required|integer|min:1',
                'max_visits_per_day' => 'required|integer|min:1|lte:max_visits',
                'rules_text' => 'nullable|string',
                'minimum_sales_amount_applies_for' => 'required|in:Single,All'
            ]);

            // 2. Get posid and logged-in user id
            $posid = Auth::user()->posid;
            $userId = Auth::id();

            // 3. Add created_by / modified_by based on existence
            $existing = LoyaltySetting::where('posid', $posid)->first();

            if ($existing) {
                // Update existing
                $validated['modified_by'] = $userId;
                $settings = $existing->update($validated);
                $settings = $existing; // return model
            } else {
                // Create new
                $validated['posid'] = $posid;
                $validated['created_by'] = $userId;
                $settings = LoyaltySetting::create($validated);
            }

            // js will refresh the page
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // 5. Return success response
            return response()->json([
                'status' => 'success',
                'message' => 'Loyalty settings saved successfully.',
                'data' => $settings
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateSmsTemplate(Request $request)
    {
        try {
            $validated = $request->validate([
                'template' => 'required|string|max:500',
            ], [], [
                'template' => 'SMS Template',
            ]);

            $posid = auth()->user()->posid;
            $userId = auth()->id();
            
            // Validate placeholder exists
            $templateBuilder = new SmsTemplateBuilder();
            $placeholderValidation = $templateBuilder->validateTemplatePlaceholder($validated['template']);
            
            if (!$placeholderValidation['valid']) {
                return response()->json([
                    'status' => 'error',
                    'errors' => [
                        'template' => ['The SMS template must contain the system-generated placeholder [## ... ##]. Please do not remove or alter this placeholder.']
                    ]
                ], 422);
            }

            // Validate template length
            $lengthValidation = $templateBuilder->validateTemplateLength($validated['template'], 80);
            
            if (!$lengthValidation['valid']) {
                return response()->json([
                    'status' => 'error',
                    'errors' => [
                        'template' => ['The SMS template is too long. Estimated final length: ' . $lengthValidation['estimated_length'] . ' characters. Maximum allowed: ' . $lengthValidation['limit'] . ' characters.']
                    ]
                ], 422);
            }

            // Find or create SMS template
            $smsTemplate = SmsTemplate::where('posid', $posid)->first();

            if ($smsTemplate) {
                // Update existing
                $smsTemplate->template = $validated['template'];
                $smsTemplate->updated_by = $userId;
                $smsTemplate->save();
            } else {
                // Create new
                $smsTemplate = SmsTemplate::create([
                    'posid' => $posid,
                    'template' => $validated['template'],
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);
            }

            // js will refresh the page
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json([
                'status' => 'success',
                'message' => 'SMS template updated successfully.',
                'data' => $smsTemplate
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateSmsConfig(Request $request)
    {
        try {
            $validated = $request->validate([
                'base_url' => 'required|string|url|max:255',
                'username' => 'required|string|max:100',
                'api_key' => 'required|string|max:255',
                'sender_id' => 'required|string|max:50',
                'campaign_id' => 'nullable|string|max:50',
                'is_active' => 'nullable|boolean',
            ], [], [
                'base_url' => 'Base URL',
                'username' => 'Username',
                'api_key' => 'API Key',
                'sender_id' => 'Sender ID',
                'campaign_id' => 'Campaign ID',
                'is_active' => 'Active Status',
            ]);

            $posid = auth()->user()->posid;
            $userId = auth()->id();

            // Find or create SMS config
            $smsConfig = SmsConfig::where('posid', $posid)->first();

            if ($smsConfig) {
                // Update existing
                $smsConfig->base_url = $validated['base_url'];
                $smsConfig->username = $validated['username'];
                $smsConfig->api_key = $validated['api_key'];
                $smsConfig->sender_id = $validated['sender_id'];
                $smsConfig->campaign_id = $validated['campaign_id'] ?? 'null';
                $smsConfig->is_active = $validated['is_active'] ?? true;
                $smsConfig->updated_by = $userId;
                $smsConfig->save();
            } else {
                // Create new
                $smsConfig = SmsConfig::create([
                    'posid' => $posid,
                    'base_url' => $validated['base_url'],
                    'username' => $validated['username'],
                    'api_key' => $validated['api_key'],
                    'sender_id' => $validated['sender_id'],
                    'campaign_id' => $validated['campaign_id'] ?? 'null',
                    'is_active' => $validated['is_active'] ?? true,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);
            }

            // js will refresh the page
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json([
                'status' => 'success',
                'message' => 'SMS configuration updated successfully.',
                'data' => $smsConfig
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }

}