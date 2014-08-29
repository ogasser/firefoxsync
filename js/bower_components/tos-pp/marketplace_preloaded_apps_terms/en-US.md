## Preload Prerequisites

December 3, 2013

In order for an application to be considered for pre-load the developer must
agree to resolve bugs/fixes in a timely manner as defined in Section 1 and the
app must fulfill the technical requirements defined in Section 2.

## Service Level Terms

Fixes/defects found by Mozilla, Operators or handset manufacturers will be
reported to the developer via Bugzilla. The developer agrees to fix these bugs
in a timely manner as defined here:

<table>
  <thead>
    <tr>
      <th>
        Priority
      </th>
      <th>
        Description
      </th>
      <th>
        Time to Resolve
      </th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>
        P1 = Critical
      </td>
      <td>
        Critical bugs are the bugs we need to fix immediately or the app will not
        be pre-loaded by one or more Operators. These are bugs that prevent basic use
        cases of the App from working if it were released in that state.
      </td>
      <td>
        Fix to be submitted to Marketplace within 3 business days of assignment
        to 3rd party Developer via Bugzilla.
      </td>
    </tr>
    <tr>
      <td>
        P2 = Major
      </td>
      <td>
        Major bugs are still bugs, but the basic use cases of the app are in tact.
        Major bugs may not result in an app being pulled from pre-load status but need
        to be fixed asap. Typically there is an end-user workaround or a quick fix that
        can be applied post-release.
      </td>
      <td>
        Fix to be submitted to Marketplace within 5 business days of assignment
        to 3rd party Developer via Bugzilla.
      </td>
    </tr>
    <tr>
      <td>
        P3 = Minor
      </td>
      <td>
        Cosmetic and minor issues such as button size, misspelled word, etc.
      </td>
      <td>
        Fix to be submitted to Marketplace within 10 business days of assignment to 3rd
        party Developer via Bugzilla.
      </td>
    </tr>
    <tr>
      <td>
        P4 = Enhancement
      </td>
      <td>
        Reserved for enhancements or "nice to haves".
      </td>
      <td>
        Developer to provide date for new version of app if the item is
        accepted on their roadmap.
      </td>
    </tr>
  </tbody>
</table>

For 3rd Party Apps, Bugs Severity Levels are designated using the Priority
Field in Bugzilla.

3rd Party apps will not block a device from shipping; however the final
decision is with the OEM/Carrier. Instead of blocking Terminal Acceptance (TA),
the expectation is that the app would be pulled from pre-load.

## Technical Requirements for Pre-load Apps

<table>
  <thead>
    <tr>
      <th>
        Area
      </th>
      <th>
        Technical Requirement
      </th>
      <th>
        Consumer Experience Benefit
      </th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>
        Startup Time
      </td>
      <td>
        If an operation will take more than a few seconds, a progress indicator
        should be used.

        <p><a href="https://developer.mozilla.org/docs/Mozilla/Firefox_OS/Performance">
          Performance
        </a></p>
      </td>
      <td>
        Progress indicators are an interactive system's way of keeping its side of
        the expected conversational protocol: "I'm working on the problem.
        Here's how much progress I've made and an indication of how much more
        time it will take."
      </td>
    </tr>
    <tr>
      <td>
        Fully Offline Apps
      </td>
      <td>
        Apps that do not require internet connectivity should be fully packaged and
        100% available offline (i.e. no internet connection required to use). This
        normally applies to apps such as Games, Utilities.
      </td>
      <td>
        Ensures that apps that do not need internet connectivity run as Consumers
        expect and are similar to comparative platforms.
      </td>
    </tr>
    <tr>
      <td>
        Offline Operation
      </td>
      <td>
        All static components (logos, menu text etc) should be stored locally
        to enable offline operation

        Apps that have NO live data should operate 100% offline (e.g. Games, Utilities)
      </td>
      <td>
        Consumer is able to see the application framework, without requiring a network
        connection/while waiting for live data components to pre-load.

        Consumer is also able to see the application framework at unboxing without
        a network connection. In addition to caching, an app will be fully viewable when
        no network connection exists.
      </td>
    </tr>
    <tr>
      <td>
        Caching
      </td>
      <td>
        App should support <strong>appcache</strong> caching as minimum so previous pages
        viewed are cached. Ideally indexedDB (local storage) should be used to store
        information.

        The previous session's app data should be cached and displayed when
        there is no internet access and if:

        <ul>
          <li>
            The app is put into the background and resumed
          </li>
          <li>
            The app is closed and restarted
          </li>
          <li>
           The device is powered off and restarted
          </li>
        </ul>

        When no connection is available (that can be detected by checking whether
        the device is online or not), the application should load the cached content
        (this is automatically done when appcache is implemented) and show a message to
        the user indicating the app requires connectivity within the app.

        On first usage of the app (when no data has been cached) the app should show
        Firefox OS "user friendly" messages e.g. "This app needs an internet
        connection" and NOT browser errors.

        <p><a href="https://developer.mozilla.org/docs/HTML/Using_the_application_cache">
          Application Cache
        </a></p>
      </td>
      <td>
        Consumer is able to see the last set of data when no internet connectivity is
        available/slow to load refreshed data.
      </td>
    </tr>
    <tr>
      <td>
        Language
      </td>
      <td>
        App must support local language of the country where the app is
        pre-loaded across all static/non-live app assets (e.g. menu items, Terms and
        Conditions, instructions etc).

        App must detect language based on device setting.
      </td>
      <td>
        Consumers can utilize apps in local languages in target countries.
      </td>
    </tr>
    <tr>
      <td>
        Sound
      </td>
      <td>
        Apps should support sound, where applicable (e.g. Games).
      </td>
      <td>
      </td>
    </tr>
    <tr>
      <td>
        UI
      </td>
      <td>
        App needs to support a full-touch UI environment and provide sufficient quality
        (e.g. menus, buttons, elements are correct size).

        <p><a href="https://developer.mozilla.org/docs/Mozilla/Firefox_OS/UX">
          UI Guidelines
        </a></p>
      </td>
      <td>
      </td>
    </tr>
  <tbody>
</table>
